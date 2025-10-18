<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Models\User;
use App\Models\QrCode;
use Carbon\Carbon;

class CacheService
{
    /**
     * Prefixo para todas as chaves de cache
     */
    const CACHE_PREFIX = 'qrcode_saas:';

    /**
     * TTL padrão para cache (em segundos)
     */
    const DEFAULT_TTL = 3600; // 1 hora

    /**
     * TTL para estatísticas (em segundos)
     */
    const STATS_TTL = 1800; // 30 minutos

    /**
     * TTL para dados de usuário (em segundos)
     */
    const USER_TTL = 7200; // 2 horas

    /**
     * TTL para QR Codes (em segundos)
     */
    const QR_CODE_TTL = 14400; // 4 horas

    /**
     * Cache de estatísticas do dashboard
     */
    public function getDashboardStats(User $user): array
    {
        $cacheKey = self::CACHE_PREFIX . "dashboard_stats:{$user->id}";

        return Cache::remember($cacheKey, self::STATS_TTL, function () use ($user) {
            return [
                'total_qr_codes' => $user->qrCodes()->count(),
                'total_scans' => $user->qrCodes()->sum('scans_count'),
                'unique_scans' => $user->qrCodes()
                    ->join('qr_scans', 'qr_codes.id', '=', 'qr_scans.qr_code_id')
                    ->where('qr_scans.is_unique', true)
                    ->count(),
                'scans_today' => $user->qrCodes()
                    ->join('qr_scans', 'qr_codes.id', '=', 'qr_scans.qr_code_id')
                    ->whereDate('qr_scans.scanned_at', today())
                    ->count(),
                'scans_this_week' => $user->qrCodes()
                    ->join('qr_scans', 'qr_codes.id', '=', 'qr_scans.qr_code_id')
                    ->whereBetween('qr_scans.scanned_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ])
                    ->count(),
                'scans_this_month' => $user->qrCodes()
                    ->join('qr_scans', 'qr_codes.id', '=', 'qr_scans.qr_code_id')
                    ->whereBetween('qr_scans.scanned_at', [
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    ])
                    ->count(),
            ];
        });
    }

    /**
     * Cache de estatísticas de um QR Code específico
     */
    public function getQrCodeStats(QrCode $qrCode): array
    {
        $cacheKey = self::CACHE_PREFIX . "qr_stats:{$qrCode->id}";

        return Cache::remember($cacheKey, self::STATS_TTL, function () use ($qrCode) {
            $scans = $qrCode->scans();
            
            return [
                'total_scans' => $scans->count(),
                'unique_scans' => $scans->where('is_unique', true)->count(),
                'scans_today' => $scans->whereDate('scanned_at', today())->count(),
                'scans_this_week' => $scans->whereBetween('scanned_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'scans_this_month' => $scans->whereBetween('scanned_at', [
                    now()->startOfMonth(),
                    now()->endOfMonth()
                ])->count(),
                'top_countries' => $scans->selectRaw('country, COUNT(*) as count')
                    ->whereNotNull('country')
                    ->groupBy('country')
                    ->orderByDesc('count')
                    ->limit(10)
                    ->get(),
                'top_cities' => $scans->selectRaw('city, COUNT(*) as count')
                    ->whereNotNull('city')
                    ->groupBy('city')
                    ->orderByDesc('count')
                    ->limit(10)
                    ->get(),
                'device_types' => $scans->selectRaw('device_type, COUNT(*) as count')
                    ->whereNotNull('device_type')
                    ->groupBy('device_type')
                    ->orderByDesc('count')
                    ->get(),
                'browsers' => $scans->selectRaw('browser, COUNT(*) as count')
                    ->whereNotNull('browser')
                    ->groupBy('browser')
                    ->orderByDesc('count')
                    ->limit(10)
                    ->get(),
                'operating_systems' => $scans->selectRaw('os, COUNT(*) as count')
                    ->whereNotNull('os')
                    ->groupBy('os')
                    ->orderByDesc('count')
                    ->limit(10)
                    ->get(),
            ];
        });
    }

    /**
     * Cache de dados de usuário
     */
    public function getUserData(User $user): array
    {
        $cacheKey = self::CACHE_PREFIX . "user_data:{$user->id}";

        return Cache::remember($cacheKey, self::USER_TTL, function () use ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'subscription_status' => $user->subscription_status,
                'trial_ends_at' => $user->trial_ends_at,
                'has_active_subscription' => $user->hasActiveSubscription(),
                'can_access_advanced_features' => $user->canAccessAdvancedFeatures(),
                'folders_count' => $user->folders()->count(),
                'teams_count' => $user->teams()->count(),
            ];
        });
    }

    /**
     * Cache de QR Codes do usuário
     */
    public function getUserQrCodes(User $user, int $limit = 10): array
    {
        $cacheKey = self::CACHE_PREFIX . "user_qr_codes:{$user->id}:{$limit}";

        return Cache::remember($cacheKey, self::QR_CODE_TTL, function () use ($user, $limit) {
            return $user->qrCodes()
                ->with(['folder', 'team'])
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    /**
     * Cache de QR Codes mais escaneados
     */
    public function getTopScannedQrCodes(User $user, int $limit = 5): array
    {
        $cacheKey = self::CACHE_PREFIX . "top_scanned_qr_codes:{$user->id}:{$limit}";

        return Cache::remember($cacheKey, self::STATS_TTL, function () use ($user, $limit) {
            return $user->qrCodes()
                ->orderByDesc('scans_count')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    /**
     * Cache de gráficos de scans ao longo do tempo
     */
    public function getScansTimeline(User $user, int $days = 30): array
    {
        $cacheKey = self::CACHE_PREFIX . "scans_timeline:{$user->id}:{$days}";

        return Cache::remember($cacheKey, self::STATS_TTL, function () use ($user, $days) {
            $startDate = now()->subDays($days);
            
            return $user->qrCodes()
                ->join('qr_scans', 'qr_codes.id', '=', 'qr_scans.qr_code_id')
                ->selectRaw('DATE(qr_scans.scanned_at) as date, COUNT(*) as scans')
                ->where('qr_scans.scanned_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->toArray();
        });
    }

    /**
     * Cache de dados de equipe
     */
    public function getTeamData(int $teamId): array
    {
        $cacheKey = self::CACHE_PREFIX . "team_data:{$teamId}";

        return Cache::remember($cacheKey, self::USER_TTL, function () use ($teamId) {
            $team = \App\Models\Team::with(['owner', 'members'])->find($teamId);
            
            if (!$team) {
                return [];
            }

            return [
                'id' => $team->id,
                'name' => $team->name,
                'description' => $team->description,
                'owner' => $team->owner,
                'members_count' => $team->members()->count(),
                'qr_codes_count' => $team->qrCodes()->count(),
            ];
        });
    }

    /**
     * Cache de configurações do sistema
     */
    public function getSystemConfig(): array
    {
        $cacheKey = self::CACHE_PREFIX . 'system_config';

        return Cache::remember($cacheKey, self::DEFAULT_TTL, function () {
            return [
                'app_name' => config('app.name'),
                'app_url' => config('app.url'),
                'trial_days' => 7,
                'premium_plan_price' => 29.90,
                'max_qr_codes_free' => null, // ilimitado
                'max_qr_codes_premium' => null, // ilimitado
                'supported_formats' => ['png', 'jpg', 'svg', 'eps'],
                'max_resolution' => 2000,
                'min_resolution' => 100,
            ];
        });
    }

    /**
     * Invalidar cache de estatísticas do usuário
     */
    public function invalidateUserStats(User $user): void
    {
        $patterns = [
            self::CACHE_PREFIX . "dashboard_stats:{$user->id}",
            self::CACHE_PREFIX . "user_data:{$user->id}",
            self::CACHE_PREFIX . "user_qr_codes:{$user->id}:*",
            self::CACHE_PREFIX . "top_scanned_qr_codes:{$user->id}:*",
            self::CACHE_PREFIX . "scans_timeline:{$user->id}:*",
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($pattern, '*')) {
                $this->invalidateByPattern($pattern);
            } else {
                Cache::forget($pattern);
            }
        }
    }

    /**
     * Invalidar cache de estatísticas de um QR Code
     */
    public function invalidateQrCodeStats(QrCode $qrCode): void
    {
        $cacheKey = self::CACHE_PREFIX . "qr_stats:{$qrCode->id}";
        Cache::forget($cacheKey);
        
        // Também invalidar cache do usuário
        $this->invalidateUserStats($qrCode->user);
    }

    /**
     * Invalidar cache por padrão (usando Redis)
     */
    private function invalidateByPattern(string $pattern): void
    {
        if (config('cache.default') === 'redis') {
            $keys = Redis::keys($pattern);
            if (!empty($keys)) {
                Redis::del($keys);
            }
        }
    }

    /**
     * Limpar todo o cache do sistema
     */
    public function clearAllCache(): void
    {
        if (config('cache.default') === 'redis') {
            $keys = Redis::keys(self::CACHE_PREFIX . '*');
            if (!empty($keys)) {
                Redis::del($keys);
            }
        } else {
            Cache::flush();
        }
    }

    /**
     * Cache de contadores de scans em tempo real
     */
    public function incrementScanCount(QrCode $qrCode): void
    {
        $cacheKey = self::CACHE_PREFIX . "scan_count:{$qrCode->id}";
        Cache::increment($cacheKey, 1);
        Cache::expire($cacheKey, 3600); // Expira em 1 hora
    }

    /**
     * Obter contador de scans em cache
     */
    public function getScanCount(QrCode $qrCode): int
    {
        $cacheKey = self::CACHE_PREFIX . "scan_count:{$qrCode->id}";
        return Cache::get($cacheKey, 0);
    }

    /**
     * Cache de dados de geolocalização
     */
    public function getLocationData(string $ip): ?array
    {
        $cacheKey = self::CACHE_PREFIX . "location:{$ip}";

        return Cache::remember($cacheKey, 86400, function () use ($ip) { // Cache por 24 horas
            // Aqui você faria a chamada para a API de geolocalização
            // Por enquanto, retornamos null
            return null;
        });
    }

    /**
     * Cache de dados de dispositivo
     */
    public function getDeviceData(string $userAgent): array
    {
        $cacheKey = self::CACHE_PREFIX . "device:" . md5($userAgent);

        return Cache::remember($cacheKey, 86400, function () use ($userAgent) { // Cache por 24 horas
            $agent = new \Jenssegers\Agent\Agent();
            $agent->setUserAgent($userAgent);

            return [
                'device_type' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
                'browser' => $agent->browser(),
                'os' => $agent->platform(),
                'is_robot' => $agent->isRobot(),
            ];
        });
    }
}
