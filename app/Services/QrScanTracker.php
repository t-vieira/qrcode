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
        $browserVersion = $this->agent->version($browser);
        $osVersion = $this->agent->version($os);
        $deviceModel = $this->getDeviceModel();
        $isRobot = $this->agent->isRobot();
        
        // Obter idioma do navegador
        $language = $request->getPreferredLanguage();
        
        // Obter referer e protocolo
        $referer = $request->header('referer');
        $protocol = $request->getScheme();
        
        // Obter geolocalização expandida
        $location = $this->getLocation($ipAddress);
        
        // Verificar se é um scan único
        $isUnique = $this->isUniqueScan($qrCode, $ipAddress, $deviceType);
        
        // Criar registro do scan
        $scan = QrScan::create([
            'qr_code_id' => $qrCode->id,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'device_type' => $deviceType,
            'device_model' => $deviceModel,
            'os' => $os,
            'os_version' => $osVersion,
            'browser' => $browser,
            'browser_version' => $browserVersion,
            'is_robot' => $isRobot,
            'country' => $location['country'] ?? null,
            'city' => $location['city'] ?? null,
            'region' => $location['region'] ?? null,
            'region_code' => $location['regionCode'] ?? null,
            'postal_code' => $location['zip'] ?? null,
            'timezone' => $location['timezone'] ?? null,
            'latitude' => $location['latitude'] ?? null,
            'longitude' => $location['longitude'] ?? null,
            'isp' => $location['isp'] ?? null,
            'organization' => $location['org'] ?? null,
            'as_number' => $location['as'] ?? null,
            'is_mobile_connection' => $location['mobile'] ?? false,
            'is_proxy' => $location['proxy'] ?? false,
            'is_hosting' => $location['hosting'] ?? false,
            'language' => $language,
            'referer' => $referer,
            'protocol' => $protocol,
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

    protected function getDeviceModel(): ?string
    {
        $device = $this->agent->device();
        
        // Se não conseguir o modelo específico, tenta obter mais informações
        if (empty($device)) {
            // Para mobile, tenta obter mais informações
            if ($this->agent->isMobile()) {
                $platform = $this->agent->platform();
                // Alguns devices podem ter mais informações no user agent
                $userAgent = $this->agent->getUserAgent();
                
                // Tenta extrair modelo de iPhone
                if (preg_match('/iPhone\s*([0-9,\s]+)/i', $userAgent, $matches)) {
                    return 'iPhone ' . trim($matches[1]);
                }
                
                // Tenta extrair modelo de Android
                if (preg_match('/;\s*([A-Z][a-z]+[A-Z]?[0-9]+[a-z0-9]*)\s*Build/i', $userAgent, $matches)) {
                    return trim($matches[1]);
                }
                
                return $platform ? "Mobile ({$platform})" : 'Mobile';
            }
        }
        
        return $device;
    }

    protected function getLocation(string $ipAddress): array
    {
        // Verificar se é IP local
        if ($this->isLocalIp($ipAddress)) {
            return [
                'country' => 'BR',
                'city' => 'São Paulo',
                'region' => 'São Paulo',
                'regionCode' => 'SP',
                'timezone' => 'America/Sao_Paulo',
                'latitude' => -23.5505,
                'longitude' => -46.6333,
                'isp' => 'Local Network',
                'org' => 'Local',
                'mobile' => false,
                'proxy' => false,
                'hosting' => false,
            ];
        }
        
        // Cache da localização por 24 horas
        $cacheKey = "location_{$ipAddress}";
        
        return Cache::remember($cacheKey, 86400, function () use ($ipAddress) {
            try {
                // Usar ip-api.com (gratuito) - retorna mais informações
                // Campos: status,message,continent,continentCode,country,countryCode,region,regionName,city,district,zip,lat,lon,timezone,offset,currency,isp,org,as,asname,mobile,proxy,hosting,query
                $response = Http::timeout(10)->get("http://ip-api.com/json/{$ipAddress}?fields=status,message,country,countryCode,region,regionName,city,zip,lat,lon,timezone,isp,org,as,mobile,proxy,hosting");
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['status']) && $data['status'] === 'success') {
                        \Log::info('Geolocalização obtida com sucesso', [
                            'ip' => $ipAddress,
                            'country' => $data['countryCode'] ?? null,
                            'city' => $data['city'] ?? null,
                            'isp' => $data['isp'] ?? null,
                        ]);
                        
                        return [
                            'country' => $data['countryCode'] ?? null,
                            'city' => $data['city'] ?? null,
                            'region' => $data['regionName'] ?? null,
                            'regionCode' => $data['region'] ?? null,
                            'zip' => $data['zip'] ?? null,
                            'timezone' => $data['timezone'] ?? null,
                            'latitude' => $data['lat'] ?? null,
                            'longitude' => $data['lon'] ?? null,
                            'isp' => $data['isp'] ?? null,
                            'org' => $data['org'] ?? null,
                            'as' => $data['as'] ?? null,
                            'mobile' => $data['mobile'] ?? false,
                            'proxy' => $data['proxy'] ?? false,
                            'hosting' => $data['hosting'] ?? false,
                        ];
                    } else {
                        \Log::warning('API de geolocalização retornou erro', [
                            'ip' => $ipAddress,
                            'message' => $data['message'] ?? 'Unknown error'
                        ]);
                    }
                } else {
                    \Log::warning('Falha na requisição de geolocalização', [
                        'ip' => $ipAddress,
                        'status' => $response->status()
                    ]);
                }
            } catch (\Exception $e) {
                // Log do erro para debug
                \Log::warning('Erro ao obter geolocalização', [
                    'ip' => $ipAddress,
                    'error' => $e->getMessage()
                ]);
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
