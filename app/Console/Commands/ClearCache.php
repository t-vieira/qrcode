<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use App\Services\CacheService;
use App\Services\PerformanceService;

class ClearCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-all {--user= : Clear cache for specific user ID} {--type= : Clear specific cache type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all application cache including custom cache services';

    /**
     * Execute the console command.
     */
    public function handle(CacheService $cacheService, PerformanceService $performanceService)
    {
        $this->info('Starting cache cleanup...');

        if ($userId = $this->option('user')) {
            $this->clearUserCache($userId, $cacheService, $performanceService);
        } elseif ($type = $this->option('type')) {
            $this->clearCacheByType($type, $cacheService);
        } else {
            $this->clearAllCache($cacheService, $performanceService);
        }

        $this->info('Cache cleanup completed successfully!');
    }

    /**
     * Clear cache for specific user
     */
    private function clearUserCache(int $userId, CacheService $cacheService, PerformanceService $performanceService): void
    {
        $this->info("Clearing cache for user ID: {$userId}");

        try {
            $user = \App\Models\User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found");
                return;
            }

            $cacheService->invalidateUserStats($user);
            $performanceService->clearOptimizationCache($user);

            $this->line("✓ Cleared cache for user: {$user->name}");
        } catch (\Exception $e) {
            $this->error("✗ Failed to clear user cache: " . $e->getMessage());
        }
    }

    /**
     * Clear cache by type
     */
    private function clearCacheByType(string $type, CacheService $cacheService): void
    {
        $this->info("Clearing cache type: {$type}");

        switch ($type) {
            case 'dashboard':
                $this->clearDashboardCache();
                break;
            case 'qr-codes':
                $this->clearQrCodeCache();
                break;
            case 'stats':
                $this->clearStatsCache();
                break;
            case 'performance':
                $this->clearPerformanceCache();
                break;
            case 'system':
                $this->clearSystemCache();
                break;
            default:
                $this->error("Unknown cache type: {$type}");
                return;
        }

        $this->line("✓ Cleared {$type} cache");
    }

    /**
     * Clear all cache
     */
    private function clearAllCache(CacheService $cacheService, PerformanceService $performanceService): void
    {
        $this->info('Clearing all application cache...');

        // Clear Laravel cache
        $this->clearLaravelCache();

        // Clear custom cache services
        $this->clearCustomCache($cacheService, $performanceService);

        // Clear view cache
        $this->clearViewCache();

        // Clear route cache
        $this->clearRouteCache();

        // Clear config cache
        $this->clearConfigCache();

        $this->line('✓ All cache cleared successfully');
    }

    /**
     * Clear Laravel cache
     */
    private function clearLaravelCache(): void
    {
        try {
            Artisan::call('cache:clear');
            $this->line('✓ Laravel cache cleared');
        } catch (\Exception $e) {
            $this->error('✗ Failed to clear Laravel cache: ' . $e->getMessage());
        }
    }

    /**
     * Clear custom cache services
     */
    private function clearCustomCache(CacheService $cacheService, PerformanceService $performanceService): void
    {
        try {
            $cacheService->clearAllCache();
            $this->line('✓ Custom cache services cleared');
        } catch (\Exception $e) {
            $this->error('✗ Failed to clear custom cache: ' . $e->getMessage());
        }
    }

    /**
     * Clear view cache
     */
    private function clearViewCache(): void
    {
        try {
            Artisan::call('view:clear');
            $this->line('✓ View cache cleared');
        } catch (\Exception $e) {
            $this->error('✗ Failed to clear view cache: ' . $e->getMessage());
        }
    }

    /**
     * Clear route cache
     */
    private function clearRouteCache(): void
    {
        try {
            Artisan::call('route:clear');
            $this->line('✓ Route cache cleared');
        } catch (\Exception $e) {
            $this->error('✗ Failed to clear route cache: ' . $e->getMessage());
        }
    }

    /**
     * Clear config cache
     */
    private function clearConfigCache(): void
    {
        try {
            Artisan::call('config:clear');
            $this->line('✓ Config cache cleared');
        } catch (\Exception $e) {
            $this->error('✗ Failed to clear config cache: ' . $e->getMessage());
        }
    }

    /**
     * Clear dashboard cache
     */
    private function clearDashboardCache(): void
    {
        $this->clearCacheByPattern('qrcode_saas:dashboard_stats:*');
        $this->clearCacheByPattern('dashboard_optimized:*');
    }

    /**
     * Clear QR Code cache
     */
    private function clearQrCodeCache(): void
    {
        $this->clearCacheByPattern('qrcode_saas:qr_stats:*');
        $this->clearCacheByPattern('qrcode_saas:user_qr_codes:*');
    }

    /**
     * Clear statistics cache
     */
    private function clearStatsCache(): void
    {
        $this->clearCacheByPattern('qrcode_saas:scans_timeline:*');
        $this->clearCacheByPattern('qrcode_saas:top_scanned_qr_codes:*');
        $this->clearCacheByPattern('scans_timeline_optimized:*');
        $this->clearCacheByPattern('top_scanned_qr_codes_optimized:*');
    }

    /**
     * Clear performance cache
     */
    private function clearPerformanceCache(): void
    {
        $this->clearCacheByPattern('*_optimized:*');
        $this->clearCacheByPattern('qrcode_saas:user_data:*');
    }

    /**
     * Clear system cache
     */
    private function clearSystemCache(): void
    {
        $this->clearCacheByPattern('qrcode_saas:system_config');
        $this->clearCacheByPattern('qrcode_saas:location:*');
        $this->clearCacheByPattern('qrcode_saas:device:*');
    }

    /**
     * Clear cache by pattern
     */
    private function clearCacheByPattern(string $pattern): void
    {
        if (config('cache.default') === 'redis') {
            try {
                $keys = \Redis::keys($pattern);
                if (!empty($keys)) {
                    \Redis::del($keys);
                }
            } catch (\Exception $e) {
                $this->error("✗ Failed to clear cache pattern {$pattern}: " . $e->getMessage());
            }
        }
    }
}