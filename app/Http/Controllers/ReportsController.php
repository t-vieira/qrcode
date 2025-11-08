<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\QrScan;
use App\Models\QrCode;

class ReportsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Estatísticas gerais
        $total_qr_codes = $user->qrCodes()->count();
        $total_scans = $user->qrCodes()->sum('scan_count') ?? 0;
        
        // Scans reais da tabela qr_scans
        $today_scans = QrScan::whereHas('qrCode', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereDate('scanned_at', today())->count();
        
        $this_week_scans = QrScan::whereHas('qrCode', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereBetween('scanned_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        
        $this_month_scans = QrScan::whereHas('qrCode', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereMonth('scanned_at', now()->month)
          ->whereYear('scanned_at', now()->year)->count();
        
        $stats = [
            'total_qr_codes' => $total_qr_codes,
            'total_scans' => $total_scans,
            'today_scans' => $today_scans,
            'this_week_scans' => $this_week_scans,
            'this_month_scans' => $this_month_scans,
        ];

        // QR Codes mais escaneados (usando dados reais da tabela qr_scans)
        $top_qr_codes = $user->qrCodes()
            ->with('folder')
            ->withCount('scans')
            ->orderBy('scans_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($qrcode) {
                $qrcode->loadStats();
                return $qrcode;
            });
        
        // Últimos scans (20 mais recentes)
        $recent_scans = QrScan::whereHas('qrCode', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with('qrCode:id,name,type')
            ->latest('scanned_at')
            ->limit(20)
            ->get();

        // Scans por dia dos últimos 30 dias (dados reais)
        $scans_by_day = QrScan::whereHas('qrCode', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->select(
            DB::raw('DATE(scanned_at) as date'),
            DB::raw('COUNT(*) as scans')
        )
        ->where('scanned_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // Scans por tipo de QR Code (dados reais)
        $scans_by_type = QrScan::whereHas('qrCode', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->join('qr_codes', 'qr_scans.qr_code_id', '=', 'qr_codes.id')
        ->select('qr_codes.type', DB::raw('COUNT(*) as total_scans'))
        ->groupBy('qr_codes.type')
        ->orderBy('total_scans', 'desc')
        ->get();

        // Scans por dispositivo (dados reais)
        $scans_by_device = QrScan::whereHas('qrCode', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->select('device_type', DB::raw('COUNT(*) as scans'))
        ->whereNotNull('device_type')
        ->groupBy('device_type')
        ->orderBy('scans', 'desc')
        ->get()
        ->map(function($item) {
            return [
                'device' => ucfirst($item->device_type),
                'scans' => $item->scans
            ];
        });

        // Scans por país (dados reais)
        $scans_by_country = QrScan::whereHas('qrCode', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->select('country', DB::raw('COUNT(*) as scans'))
        ->whereNotNull('country')
        ->groupBy('country')
        ->orderBy('scans', 'desc')
        ->limit(5)
        ->get()
        ->map(function($item) {
            return [
                'country' => $this->getCountryName($item->country),
                'scans' => $item->scans
            ];
        });

        return view('reports.index', compact(
            'stats',
            'top_qr_codes',
            'recent_scans',
            'scans_by_day',
            'scans_by_type',
            'scans_by_device',
            'scans_by_country'
        ));
    }

    private function getCountryName(string $countryCode): string
    {
        $countries = [
            'BR' => 'Brasil',
            'US' => 'Estados Unidos',
            'AR' => 'Argentina',
            'MX' => 'México',
            'CA' => 'Canadá',
            'GB' => 'Reino Unido',
            'DE' => 'Alemanha',
            'FR' => 'França',
            'IT' => 'Itália',
            'ES' => 'Espanha',
            'PT' => 'Portugal',
            'CL' => 'Chile',
            'CO' => 'Colômbia',
            'PE' => 'Peru',
            'UY' => 'Uruguai',
        ];

        return $countries[$countryCode] ?? $countryCode;
    }
}
