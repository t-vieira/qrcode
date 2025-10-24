<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\Folder;
use Illuminate\Http\Request;

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
        
        // QR Codes recentes com pasta
        $recentQrCodes = $user->qrCodes()
            ->with('folder')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Dados para o gráfico de scans
        $scansChartData = $this->getScansChartData($user);
        
        return view('dashboard', compact(
            'stats', 
            'recentQrCodes',
            'scansChartData',
            'folders'
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
