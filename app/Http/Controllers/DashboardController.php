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
        
        // Para simplificar, vamos usar valores padrão por enquanto
        // Em produção, você pode implementar as consultas reais
        return [
            'total_qr_codes' => $totalQrCodes,
            'total_scans' => 0,
            'unique_scans' => 0,
            'today_scans' => 0,
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
