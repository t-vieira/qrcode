<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Estatísticas básicas
        $stats = $this->getDashboardStats($user);
        
        // QR Codes recentes
        $recentQrCodes = $user->qrCodes()
            ->latest()
            ->limit(5)
            ->get();

        // Dados para o gráfico de scans
        $scansChartData = $this->getScansChartData($user);
        
        return view('dashboard', compact(
            'stats', 
            'recentQrCodes',
            'scansChartData'
        ));
    }

    protected function getDashboardStats($user): array
    {
        $totalQrCodes = $user->qrCodes()->count();
        
        // Buscar QR Codes do usuário
        $qrCodeIds = $user->qrCodes()->pluck('id');
        
        // Calcular estatísticas reais
        $totalScans = $user->qrCodes()->sum('scan_count');
        $uniqueScans = \App\Models\QrScan::whereIn('qr_code_id', $qrCodeIds)->distinct('ip_address')->count();
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
        // Para simplificar, vamos retornar dados vazios por enquanto
        // Em produção, você pode implementar as consultas reais
        return [
            'labels' => [],
            'data' => [],
        ];
    }

}
