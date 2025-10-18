<?php

namespace App\Services;

use App\Models\User;
use App\Models\QrCode;
use App\Models\QrScan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PerformanceService
{
    /**
     * Otimizar consultas do dashboard
     */
    public function optimizeDashboardQueries(User $user): array
    {
        $cacheKey = "dashboard_optimized:{$user->id}";
        
        return Cache::remember($cacheKey, 1800, function () use ($user) {
            // Usar consultas otimizadas com joins e agregações
            $stats = DB::table('qr_codes')
                ->leftJoin('qr_scans', 'qr_codes.id', '=', 'qr_scans.qr_code_id')
                ->where('qr_codes.user_id', $user->id)
                ->where('qr_codes.deleted_at', null)
                ->selectRaw('
                    COUNT(DISTINCT qr_codes.id) as total_qr_codes,
                    COALESCE(SUM(qr_codes.scans_count), 0) as total_scans,
                    COALESCE(SUM(CASE WHEN qr_scans.is_unique = true THEN 1 ELSE 0 END), 0) as unique_scans,
                    COALESCE(SUM(CASE WHEN DATE(qr_scans.scanned_at) = CURDATE() THEN 1 ELSE 0 END), 0) as scans_today,
                    COALESCE(SUM(CASE WHEN qr_scans.scanned_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END), 0) as scans_this_week,
                    COALESCE(SUM(CASE WHEN qr_scans.scanned_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END), 0) as scans_this_month
                ')
                ->first();

            return [
                'total_qr_codes' => $stats->total_qr_codes,
                'total_scans' => $stats->total_scans,
                'unique_scans' => $stats->unique_scans,
                'scans_today' => $stats->scans_today,
                'scans_this_week' => $stats->scans_this_week,
                'scans_this_month' => $stats->scans_this_month,
            ];
        });
    }

    /**
     * Otimizar consultas de estatísticas de QR Code
     */
    public function optimizeQrCodeStats(QrCode $qrCode): array
    {
        $cacheKey = "qr_stats_optimized:{$qrCode->id}";
        
        return Cache::remember($cacheKey, 1800, function () use ($qrCode) {
            $stats = DB::table('qr_scans')
                ->where('qr_code_id', $qrCode->id)
                ->selectRaw('
                    COUNT(*) as total_scans,
                    SUM(CASE WHEN is_unique = true THEN 1 ELSE 0 END) as unique_scans,
                    SUM(CASE WHEN DATE(scanned_at) = CURDATE() THEN 1 ELSE 0 END) as scans_today,
                    SUM(CASE WHEN scanned_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as scans_this_week,
                    SUM(CASE WHEN scanned_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as scans_this_month
                ')
                ->first();

            // Top países
            $topCountries = DB::table('qr_scans')
                ->where('qr_code_id', $qrCode->id)
                ->whereNotNull('country')
                ->selectRaw('country, COUNT(*) as count')
                ->groupBy('country')
                ->orderByDesc('count')
                ->limit(10)
                ->get();

            // Top cidades
            $topCities = DB::table('qr_scans')
                ->where('qr_code_id', $qrCode->id)
                ->whereNotNull('city')
                ->selectRaw('city, COUNT(*) as count')
                ->groupBy('city')
                ->orderByDesc('count')
                ->limit(10)
                ->get();

            // Tipos de dispositivo
            $deviceTypes = DB::table('qr_scans')
                ->where('qr_code_id', $qrCode->id)
                ->whereNotNull('device_type')
                ->selectRaw('device_type, COUNT(*) as count')
                ->groupBy('device_type')
                ->orderByDesc('count')
                ->get();

            return [
                'total_scans' => $stats->total_scans,
                'unique_scans' => $stats->unique_scans,
                'scans_today' => $stats->scans_today,
                'scans_this_week' => $stats->scans_this_week,
                'scans_this_month' => $stats->scans_this_month,
                'top_countries' => $topCountries,
                'top_cities' => $topCities,
                'device_types' => $deviceTypes,
            ];
        });
    }

    /**
     * Otimizar consultas de timeline de scans
     */
    public function optimizeScansTimeline(User $user, int $days = 30): array
    {
        $cacheKey = "scans_timeline_optimized:{$user->id}:{$days}";
        
        return Cache::remember($cacheKey, 1800, function () use ($user, $days) {
            return DB::table('qr_codes')
                ->join('qr_scans', 'qr_codes.id', '=', 'qr_scans.qr_code_id')
                ->where('qr_codes.user_id', $user->id)
                ->where('qr_codes.deleted_at', null)
                ->where('qr_scans.scanned_at', '>=', now()->subDays($days))
                ->selectRaw('DATE(qr_scans.scanned_at) as date, COUNT(*) as scans')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->toArray();
        });
    }

    /**
     * Otimizar consultas de QR Codes mais escaneados
     */
    public function optimizeTopScannedQrCodes(User $user, int $limit = 10): array
    {
        $cacheKey = "top_scanned_qr_codes_optimized:{$user->id}:{$limit}";
        
        return Cache::remember($cacheKey, 1800, function () use ($user, $limit) {
            return DB::table('qr_codes')
                ->where('user_id', $user->id)
                ->where('deleted_at', null)
                ->select('id', 'name', 'type', 'scans_count', 'created_at')
                ->orderByDesc('scans_count')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    /**
     * Otimizar consultas de relatórios
     */
    public function optimizeReportData(User $user, array $filters = []): array
    {
        $cacheKey = "report_data_optimized:{$user->id}:" . md5(serialize($filters));
        
        return Cache::remember($cacheKey, 3600, function () use ($user, $filters) {
            $query = DB::table('qr_codes')
                ->leftJoin('qr_scans', 'qr_codes.id', '=', 'qr_scans.qr_code_id')
                ->where('qr_codes.user_id', $user->id)
                ->where('qr_codes.deleted_at', null);

            // Aplicar filtros
            if (!empty($filters['date_from'])) {
                $query->where('qr_scans.scanned_at', '>=', $filters['date_from']);
            }
            
            if (!empty($filters['date_to'])) {
                $query->where('qr_scans.scanned_at', '<=', $filters['date_to']);
            }
            
            if (!empty($filters['qr_code_id'])) {
                $query->where('qr_codes.id', $filters['qr_code_id']);
            }

            return $query->selectRaw('
                qr_codes.id,
                qr_codes.name,
                qr_codes.type,
                COUNT(qr_scans.id) as total_scans,
                SUM(CASE WHEN qr_scans.is_unique = true THEN 1 ELSE 0 END) as unique_scans,
                COUNT(DISTINCT qr_scans.country) as countries_count,
                COUNT(DISTINCT qr_scans.city) as cities_count
            ')
            ->groupBy('qr_codes.id', 'qr_codes.name', 'qr_codes.type')
            ->orderByDesc('total_scans')
            ->get()
            ->toArray();
        });
    }

    /**
     * Otimizar consultas de busca
     */
    public function optimizeSearchQuery(User $user, string $searchTerm): array
    {
        $cacheKey = "search_optimized:{$user->id}:" . md5($searchTerm);
        
        return Cache::remember($cacheKey, 300, function () use ($user, $searchTerm) {
            return DB::table('qr_codes')
                ->where('user_id', $user->id)
                ->where('deleted_at', null)
                ->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('type', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('short_code', 'LIKE', "%{$searchTerm}%");
                })
                ->select('id', 'name', 'type', 'short_code', 'scans_count', 'created_at')
                ->orderByDesc('created_at')
                ->limit(50)
                ->get()
                ->toArray();
        });
    }

    /**
     * Otimizar consultas de equipes
     */
    public function optimizeTeamQueries(User $user): array
    {
        $cacheKey = "team_queries_optimized:{$user->id}";
        
        return Cache::remember($cacheKey, 3600, function () use ($user) {
            // Equipes que o usuário possui
            $ownedTeams = DB::table('teams')
                ->where('owner_id', $user->id)
                ->where('deleted_at', null)
                ->select('id', 'name', 'description', 'created_at')
                ->get();

            // Equipes que o usuário participa
            $memberTeams = DB::table('teams')
                ->join('team_user', 'teams.id', '=', 'team_user.team_id')
                ->where('team_user.user_id', $user->id)
                ->where('teams.deleted_at', null)
                ->select('teams.id', 'teams.name', 'teams.description', 'team_user.role', 'teams.created_at')
                ->get();

            return [
                'owned_teams' => $ownedTeams,
                'member_teams' => $memberTeams,
            ];
        });
    }

    /**
     * Otimizar consultas de pastas
     */
    public function optimizeFolderQueries(User $user): array
    {
        $cacheKey = "folder_queries_optimized:{$user->id}";
        
        return Cache::remember($cacheKey, 3600, function () use ($user) {
            return DB::table('folders')
                ->leftJoin('qr_codes', function ($join) {
                    $join->on('folders.id', '=', 'qr_codes.folder_id')
                         ->whereNull('qr_codes.deleted_at');
                })
                ->where('folders.user_id', $user->id)
                ->where('folders.deleted_at', null)
                ->selectRaw('
                    folders.id,
                    folders.name,
                    folders.slug,
                    COUNT(qr_codes.id) as qr_codes_count,
                    folders.created_at
                ')
                ->groupBy('folders.id', 'folders.name', 'folders.slug', 'folders.created_at')
                ->orderBy('folders.name')
                ->get()
                ->toArray();
        });
    }

    /**
     * Otimizar consultas de domínios customizados
     */
    public function optimizeCustomDomainQueries(User $user): array
    {
        $cacheKey = "custom_domains_optimized:{$user->id}";
        
        return Cache::remember($cacheKey, 3600, function () use ($user) {
            return DB::table('custom_domains')
                ->where('user_id', $user->id)
                ->select('id', 'domain', 'status', 'is_primary', 'verified_at', 'created_at')
                ->orderBy('is_primary', 'desc')
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();
        });
    }

    /**
     * Otimizar consultas de tickets de suporte
     */
    public function optimizeSupportTicketQueries(User $user): array
    {
        $cacheKey = "support_tickets_optimized:{$user->id}";
        
        return Cache::remember($cacheKey, 1800, function () use ($user) {
            return DB::table('support_tickets')
                ->where('user_id', $user->id)
                ->select('id', 'subject', 'status', 'priority', 'category', 'created_at', 'last_reply_at')
                ->orderByDesc('created_at')
                ->limit(20)
                ->get()
                ->toArray();
        });
    }

    /**
     * Limpar cache de otimizações
     */
    public function clearOptimizationCache(User $user): void
    {
        $patterns = [
            "dashboard_optimized:{$user->id}",
            "scans_timeline_optimized:{$user->id}:*",
            "top_scanned_qr_codes_optimized:{$user->id}:*",
            "report_data_optimized:{$user->id}:*",
            "search_optimized:{$user->id}:*",
            "team_queries_optimized:{$user->id}",
            "folder_queries_optimized:{$user->id}",
            "custom_domains_optimized:{$user->id}",
            "support_tickets_optimized:{$user->id}",
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($pattern, '*')) {
                $this->clearCacheByPattern($pattern);
            } else {
                Cache::forget($pattern);
            }
        }
    }

    /**
     * Limpar cache por padrão
     */
    private function clearCacheByPattern(string $pattern): void
    {
        if (config('cache.default') === 'redis') {
            $keys = \Redis::keys($pattern);
            if (!empty($keys)) {
                \Redis::del($keys);
            }
        }
    }

    /**
     * Obter métricas de performance
     */
    public function getPerformanceMetrics(): array
    {
        return [
            'database_connections' => DB::getConnections(),
            'cache_hit_rate' => $this->getCacheHitRate(),
            'slow_queries' => $this->getSlowQueries(),
            'memory_usage' => memory_get_usage(true),
            'peak_memory_usage' => memory_get_peak_usage(true),
        ];
    }

    /**
     * Obter taxa de acerto do cache
     */
    private function getCacheHitRate(): float
    {
        // Implementar lógica para calcular taxa de acerto do cache
        return 0.0;
    }

    /**
     * Obter consultas lentas
     */
    private function getSlowQueries(): array
    {
        // Implementar lógica para obter consultas lentas
        return [];
    }
}
