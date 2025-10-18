<?php

namespace App\Services;

use App\Models\QrCode;
use App\Models\QrScan;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class QrScanTracker
{
    protected Agent $agent;

    public function __construct()
    {
        $this->agent = new Agent();
    }

    public function track(QrCode $qrCode, Request $request): QrScan
    {
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        
        // Detectar informações do dispositivo
        $this->agent->setUserAgent($userAgent);
        
        $deviceType = $this->getDeviceType();
        $os = $this->agent->platform();
        $browser = $this->agent->browser();
        
        // Obter geolocalização
        $location = $this->getLocation($ipAddress);
        
        // Verificar se é um scan único
        $isUnique = $this->isUniqueScan($qrCode, $ipAddress, $deviceType);
        
        // Criar registro do scan
        $scan = QrScan::create([
            'qr_code_id' => $qrCode->id,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'device_type' => $deviceType,
            'os' => $os,
            'browser' => $browser,
            'country' => $location['country'] ?? null,
            'city' => $location['city'] ?? null,
            'latitude' => $location['latitude'] ?? null,
            'longitude' => $location['longitude'] ?? null,
            'is_unique' => $isUnique,
            'scanned_at' => now(),
        ]);
        
        return $scan;
    }

    protected function getDeviceType(): string
    {
        if ($this->agent->isMobile()) {
            return 'mobile';
        }
        
        if ($this->agent->isTablet()) {
            return 'tablet';
        }
        
        return 'desktop';
    }

    protected function getLocation(string $ipAddress): array
    {
        // Verificar se é IP local
        if ($this->isLocalIp($ipAddress)) {
            return [
                'country' => 'BR',
                'city' => 'São Paulo',
                'latitude' => -23.5505,
                'longitude' => -46.6333,
            ];
        }
        
        // Cache da localização por 24 horas
        $cacheKey = "location_{$ipAddress}";
        
        return Cache::remember($cacheKey, 86400, function () use ($ipAddress) {
            try {
                // Usar ip-api.com (gratuito)
                $response = Http::timeout(5)->get("http://ip-api.com/json/{$ipAddress}");
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    if ($data['status'] === 'success') {
                        return [
                            'country' => $data['countryCode'] ?? null,
                            'city' => $data['city'] ?? null,
                            'latitude' => $data['lat'] ?? null,
                            'longitude' => $data['lon'] ?? null,
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Log do erro se necessário
            }
            
            return [];
        });
    }

    protected function isLocalIp(string $ipAddress): bool
    {
        return in_array($ipAddress, [
            '127.0.0.1',
            '::1',
            'localhost',
        ]) || str_starts_with($ipAddress, '192.168.') || 
             str_starts_with($ipAddress, '10.') ||
             str_starts_with($ipAddress, '172.');
    }

    protected function isUniqueScan(QrCode $qrCode, string $ipAddress, string $deviceType): bool
    {
        $cacheKey = "unique_scan_{$qrCode->id}_{$ipAddress}_{$deviceType}";
        
        // Verificar se já existe no cache (últimas 24 horas)
        if (Cache::has($cacheKey)) {
            return false;
        }
        
        // Verificar no banco de dados (últimas 24 horas)
        $recentScan = QrScan::where('qr_code_id', $qrCode->id)
            ->where('ip_address', $ipAddress)
            ->where('device_type', $deviceType)
            ->where('scanned_at', '>=', now()->subDay())
            ->exists();
        
        if ($recentScan) {
            return false;
        }
        
        // Marcar como único no cache por 24 horas
        Cache::put($cacheKey, true, 86400);
        
        return true;
    }

    public function getStats(QrCode $qrCode, int $days = 30): array
    {
        $startDate = now()->subDays($days);
        
        $scans = QrScan::where('qr_code_id', $qrCode->id)
            ->where('scanned_at', '>=', $startDate)
            ->get();
        
        $totalScans = $scans->count();
        $uniqueScans = $scans->where('is_unique', true)->count();
        
        // Scans por dia
        $scansByDay = $scans->groupBy(function ($scan) {
            return $scan->scanned_at->format('Y-m-d');
        })->map->count();
        
        // Scans por país
        $scansByCountry = $scans->whereNotNull('country')
            ->groupBy('country')
            ->map->count()
            ->sortDesc();
        
        // Scans por dispositivo
        $scansByDevice = $scans->groupBy('device_type')
            ->map->count();
        
        // Scans por OS
        $scansByOs = $scans->whereNotNull('os')
            ->groupBy('os')
            ->map->count()
            ->sortDesc();
        
        // Scans por browser
        $scansByBrowser = $scans->whereNotNull('browser')
            ->groupBy('browser')
            ->map->count()
            ->sortDesc();
        
        return [
            'total_scans' => $totalScans,
            'unique_scans' => $uniqueScans,
            'scans_by_day' => $scansByDay,
            'scans_by_country' => $scansByCountry,
            'scans_by_device' => $scansByDevice,
            'scans_by_os' => $scansByOs,
            'scans_by_browser' => $scansByBrowser,
            'period' => $days,
        ];
    }
}
