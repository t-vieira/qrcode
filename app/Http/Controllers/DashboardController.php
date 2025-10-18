<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Services\QrScanTracker;
use App\Services\CacheService;
use App\Services\PerformanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    protected QrScanTracker $scanTracker;
    protected CacheService $cacheService;
    protected PerformanceService $performanceService;

    public function __construct(
        QrScanTracker $scanTracker,
        CacheService $cacheService,
        PerformanceService $performanceService
    ) {
        $this->scanTracker = $scanTracker;
        $this->cacheService = $cacheService;
        $this->performanceService = $performanceService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        
        // Usar service de cache otimizado
        $stats = $this->cacheService->getDashboardStats($user);
        
        // Usar service de performance para consultas otimizadas
        $optimizedStats = $this->performanceService->optimizeDashboardQueries($user);
        
        // QR Codes recentes (com cache)
        $recentQrCodes = $this->cacheService->getUserQrCodes($user, 5);

        // QR Codes mais escaneados (com cache)
        $topQrCodes = $this->cacheService->getTopScannedQrCodes($user, 5);
        
        // Dados para o gráfico de scans (com cache otimizado)
        $scansChartData = $this->performanceService->optimizeScansTimeline($user, 30);
        
        // Timeline de scans otimizada
        $scansTimeline = $this->cacheService->getScansTimeline($user, 30);
        
        return view('dashboard', compact(
            'stats', 
            'optimizedStats',
            'recentQrCodes', 
            'topQrCodes',
            'scansChartData',
            'scansTimeline'
        ));
    }

    protected function getDashboardStats($user): array
    {
        $totalQrCodes = $user->qrCodes()->count();
        
        $totalScans = $user->qrCodes()
            ->withCount('scans')
            ->get()
            ->sum('scans_count');
        
        $uniqueScans = $user->qrCodes()
            ->whereHas('scans', function ($query) {
                $query->where('is_unique', true);
            })
            ->withCount(['scans' => function ($query) {
                $query->where('is_unique', true);
            }])
            ->get()
            ->sum('scans_count');
        
        $todayScans = $user->qrCodes()
            ->whereHas('scans', function ($query) {
                $query->whereDate('scanned_at', today());
            })
            ->withCount(['scans' => function ($query) {
                $query->whereDate('scanned_at', today());
            }])
            ->get()
            ->sum('scans_count');
        
        return [
            'total_qr_codes' => $totalQrCodes,
            'total_scans' => $totalScans,
            'unique_scans' => $uniqueScans,
            'today_scans' => $todayScans,
        ];
    }

    protected function getScansChartData($user): array
    {
        $labels = [];
        $data = [];
        
        // Últimos 30 dias
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d/m');
            
            $scans = $user->qrCodes()
                ->whereHas('scans', function ($query) use ($date) {
                    $query->whereDate('scanned_at', $date->toDateString());
                })
                ->withCount(['scans' => function ($query) use ($date) {
                    $query->whereDate('scanned_at', $date->toDateString());
                }])
                ->get()
                ->sum('scans_count');
            
            $data[] = $scans;
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * API endpoint para dados do dashboard
     */
    public function api(Request $request)
    {
        $user = $request->user();
        
        $stats = $this->cacheService->getDashboardStats($user);
        $chartData = $this->performanceService->optimizeScansTimeline($user, 30);
        
        return response()->json([
            'stats' => $stats,
            'chart_data' => $chartData,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Invalidar cache do dashboard
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        
        $this->cacheService->invalidateUserStats($user);
        $this->performanceService->clearOptimizationCache($user);
        
        return redirect()->route('dashboard')->with('success', 'Dashboard atualizado com sucesso!');
    }

    /**
     * Obter estatísticas em tempo real
     */
    public function realtime(Request $request)
    {
        $user = $request->user();
        
        // Obter contadores em tempo real do cache
        $realtimeStats = [];
        
        foreach ($user->qrCodes as $qrCode) {
            $realtimeStats[$qrCode->id] = [
                'scans_count' => $this->cacheService->getScanCount($qrCode),
                'last_scan' => $qrCode->scans()->latest()->first()?->scanned_at,
            ];
        }
        
        return response()->json([
            'realtime_stats' => $realtimeStats,
            'timestamp' => now()->toISOString(),
        ]);
    }
}
