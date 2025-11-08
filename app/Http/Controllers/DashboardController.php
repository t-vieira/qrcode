<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\Folder;
use App\Models\QrScan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Estatísticas básicas
        $stats = $this->getDashboardStats($user);
        
        // Pastas do usuário
        $folders = $user->folders()
            ->withCount('qrCodes')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();
        
        // Query base para QR Codes
        $query = $user->qrCodes()->with('folder');
        
        // Filtro por status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Filtro por tipo
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        // Busca por nome
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Ordenação
        $orderBy = $request->get('order', 'created_at');
        $orderDirection = $request->get('direction', 'desc');
        
        // Ordenação especial para scans
        if ($orderBy === 'scans') {
            $query->withCount('scans')
                  ->orderBy('scans_count', $orderDirection);
        } elseif ($orderBy === 'last_scan') {
            // Para ordenação por último scan, vamos ordenar por created_at primeiro
            // e depois ordenar na collection após carregar as stats
            $query->orderBy('created_at', 'desc');
        } else {
            $query->orderBy($orderBy, $orderDirection);
        }
        
        // QR Codes com estatísticas detalhadas
        $recentQrCodes = $query->paginate(20);
        
        // Carregar estatísticas detalhadas para cada QR Code
        $recentQrCodes->getCollection()->transform(function ($qrcode) {
            $qrcode->loadStats();
            return $qrcode;
        });
        
        // Se ordenação por último scan, ordenar após carregar stats
        if ($orderBy === 'last_scan') {
            $sorted = $recentQrCodes->getCollection()->sortBy(function ($qrcode) {
                $lastScan = $qrcode->stats_last_scan;
                return $lastScan ? $lastScan->scanned_at->timestamp : 0;
            });
            
            if ($orderDirection === 'desc') {
                $sorted = $sorted->reverse();
            }
            
            $recentQrCodes->setCollection($sorted->values());
        }

        // QR Codes mais escaneados (top 5)
        $topScannedQrCodes = $user->qrCodes()
            ->with('folder')
            ->withCount('scans')
            ->orderBy('scans_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($qrcode) {
                $qrcode->loadStats();
                return $qrcode;
            });
        
        // Últimos scans (10 mais recentes)
        $recentScans = QrScan::whereHas('qrCode', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with('qrCode:id,name,type')
            ->latest('scanned_at')
            ->limit(10)
            ->get();

        // Dados para o gráfico de scans
        $scansChartData = $this->getScansChartData($user);
        
        // Tipos de QR Code disponíveis para filtro
        $qrCodeTypes = $user->qrCodes()
            ->select('type')
            ->distinct()
            ->pluck('type')
            ->sort()
            ->values();
        
        return view('dashboard', compact(
            'stats', 
            'recentQrCodes',
            'scansChartData',
            'folders',
            'topScannedQrCodes',
            'recentScans',
            'qrCodeTypes'
        ));
    }

    protected function getDashboardStats($user): array
    {
        $totalQrCodes = $user->qrCodes()->count();
        
        // Buscar QR Codes do usuário
        $qrCodeIds = $user->qrCodes()->pluck('id');
        
        // Calcular estatísticas reais dos scans
        $totalScans = \App\Models\QrScan::whereIn('qr_code_id', $qrCodeIds)->count();
        $uniqueScans = \App\Models\QrScan::whereIn('qr_code_id', $qrCodeIds)->where('is_unique', true)->count();
        $todayScans = \App\Models\QrScan::whereIn('qr_code_id', $qrCodeIds)
            ->whereDate('scanned_at', today())
            ->count();
        
        return [
            'total_qr_codes' => $totalQrCodes,
            'total_scans' => $totalScans,
            'unique_scans' => $uniqueScans,
            'today_scans' => $todayScans,
        ];
    }

    protected function getScansChartData($user): array
    {
        $qrCodeIds = $user->qrCodes()->pluck('id');
        
        // Gerar dados dos últimos 30 dias
        $labels = [];
        $data = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d/m');
            
            $scansCount = \App\Models\QrScan::whereIn('qr_code_id', $qrCodeIds)
                ->whereDate('scanned_at', $date->format('Y-m-d'))
                ->count();
                
            $data[] = $scansCount;
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

}
