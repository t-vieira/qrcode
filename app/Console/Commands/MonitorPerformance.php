<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\PerformanceService;
use App\Services\QueueService;

class MonitorPerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'performance:monitor {--export : Export results to file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor application performance metrics';

    /**
     * Execute the console command.
     */
    public function handle(PerformanceService $performanceService, QueueService $queueService)
    {
        $this->info('Monitoring application performance...');

        $metrics = $this->collectMetrics($performanceService, $queueService);
        $this->displayMetrics($metrics);

        if ($this->option('export')) {
            $this->exportMetrics($metrics);
        }

        $this->info('Performance monitoring completed!');
    }

    /**
     * Collect performance metrics
     */
    private function collectMetrics(PerformanceService $performanceService, QueueService $queueService): array
    {
        return [
            'database' => $this->getDatabaseMetrics(),
            'cache' => $this->getCacheMetrics(),
            'queues' => $queueService->getQueueStatus(),
            'memory' => $this->getMemoryMetrics(),
            'disk' => $this->getDiskMetrics(),
            'application' => $this->getApplicationMetrics(),
        ];
    }

    /**
     * Get database metrics
     */
    private function getDatabaseMetrics(): array
    {
        try {
            $connection = DB::connection();
            $pdo = $connection->getPdo();

            $metrics = [
                'connection_count' => $this->getConnectionCount(),
                'slow_queries' => $this->getSlowQueries(),
                'table_sizes' => $this->getTableSizes(),
                'index_usage' => $this->getIndexUsage(),
            ];

            if (config('database.default') === 'pgsql') {
                $metrics['postgres_stats'] = $this->getPostgresStats();
            } elseif (config('database.default') === 'mysql') {
                $metrics['mysql_stats'] = $this->getMysqlStats();
            }

            return $metrics;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get cache metrics
     */
    private function getCacheMetrics(): array
    {
        try {
            $driver = config('cache.default');
            $metrics = ['driver' => $driver];

            if ($driver === 'redis') {
                $metrics['redis_info'] = $this->getRedisInfo();
                $metrics['cache_keys'] = $this->getCacheKeysCount();
            } elseif ($driver === 'file') {
                $metrics['file_cache_size'] = $this->getFileCacheSize();
            }

            return $metrics;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get memory metrics
     */
    private function getMemoryMetrics(): array
    {
        return [
            'current_usage' => memory_get_usage(true),
            'peak_usage' => memory_get_peak_usage(true),
            'current_usage_formatted' => $this->formatBytes(memory_get_usage(true)),
            'peak_usage_formatted' => $this->formatBytes(memory_get_peak_usage(true)),
            'limit' => ini_get('memory_limit'),
        ];
    }

    /**
     * Get disk metrics
     */
    private function getDiskMetrics(): array
    {
        $storagePath = storage_path();
        $publicPath = public_path();

        return [
            'storage_free' => disk_free_space($storagePath),
            'storage_total' => disk_total_space($storagePath),
            'storage_used' => disk_total_space($storagePath) - disk_free_space($storagePath),
            'storage_free_formatted' => $this->formatBytes(disk_free_space($storagePath)),
            'storage_total_formatted' => $this->formatBytes(disk_total_space($storagePath)),
            'storage_used_formatted' => $this->formatBytes(disk_total_space($storagePath) - disk_free_space($storagePath)),
            'public_free' => disk_free_space($publicPath),
            'public_total' => disk_total_space($publicPath),
        ];
    }

    /**
     * Get application metrics
     */
    private function getApplicationMetrics(): array
    {
        return [
            'users_count' => \App\Models\User::count(),
            'qr_codes_count' => \App\Models\QrCode::count(),
            'scans_count' => \App\Models\QrScan::count(),
            'active_subscriptions' => \App\Models\Subscription::where('status', 'authorized')->count(),
            'trial_users' => \App\Models\User::where('subscription_status', 'trialing')->count(),
            'uptime' => $this->getUptime(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];
    }

    /**
     * Display metrics in console
     */
    private function displayMetrics(array $metrics): void
    {
        $this->newLine();
        $this->info('=== PERFORMANCE METRICS ===');
        $this->newLine();

        // Database metrics
        $this->displayDatabaseMetrics($metrics['database']);

        // Cache metrics
        $this->displayCacheMetrics($metrics['cache']);

        // Queue metrics
        $this->displayQueueMetrics($metrics['queues']);

        // Memory metrics
        $this->displayMemoryMetrics($metrics['memory']);

        // Disk metrics
        $this->displayDiskMetrics($metrics['disk']);

        // Application metrics
        $this->displayApplicationMetrics($metrics['application']);
    }

    /**
     * Display database metrics
     */
    private function displayDatabaseMetrics(array $metrics): void
    {
        $this->info('📊 DATABASE METRICS');
        $this->line('Connection Count: ' . ($metrics['connection_count'] ?? 'N/A'));
        
        if (isset($metrics['table_sizes'])) {
            $this->line('Table Sizes:');
            foreach ($metrics['table_sizes'] as $table => $size) {
                $this->line("  - {$table}: {$size}");
            }
        }

        if (isset($metrics['slow_queries'])) {
            $this->line('Slow Queries: ' . count($metrics['slow_queries']));
        }

        $this->newLine();
    }

    /**
     * Display cache metrics
     */
    private function displayCacheMetrics(array $metrics): void
    {
        $this->info('💾 CACHE METRICS');
        $this->line('Driver: ' . ($metrics['driver'] ?? 'N/A'));

        if (isset($metrics['cache_keys'])) {
            $this->line('Cache Keys: ' . $metrics['cache_keys']);
        }

        if (isset($metrics['file_cache_size'])) {
            $this->line('File Cache Size: ' . $metrics['file_cache_size']);
        }

        $this->newLine();
    }

    /**
     * Display queue metrics
     */
    private function displayQueueMetrics(array $metrics): void
    {
        $this->info('📋 QUEUE METRICS');
        
        foreach ($metrics as $queue => $status) {
            $this->line("{$queue}:");
            $this->line("  - Pending: {$status['pending']}");
            $this->line("  - Failed: {$status['failed']}");
        }

        $this->newLine();
    }

    /**
     * Display memory metrics
     */
    private function displayMemoryMetrics(array $metrics): void
    {
        $this->info('🧠 MEMORY METRICS');
        $this->line('Current Usage: ' . $metrics['current_usage_formatted']);
        $this->line('Peak Usage: ' . $metrics['peak_usage_formatted']);
        $this->line('Memory Limit: ' . $metrics['limit']);
        $this->newLine();
    }

    /**
     * Display disk metrics
     */
    private function displayDiskMetrics(array $metrics): void
    {
        $this->info('💿 DISK METRICS');
        $this->line('Storage Used: ' . $metrics['storage_used_formatted']);
        $this->line('Storage Free: ' . $metrics['storage_free_formatted']);
        $this->line('Storage Total: ' . $metrics['storage_total_formatted']);
        $this->newLine();
    }

    /**
     * Display application metrics
     */
    private function displayApplicationMetrics(array $metrics): void
    {
        $this->info('🚀 APPLICATION METRICS');
        $this->line('Users: ' . $metrics['users_count']);
        $this->line('QR Codes: ' . $metrics['qr_codes_count']);
        $this->line('Scans: ' . $metrics['scans_count']);
        $this->line('Active Subscriptions: ' . $metrics['active_subscriptions']);
        $this->line('Trial Users: ' . $metrics['trial_users']);
        $this->line('PHP Version: ' . $metrics['php_version']);
        $this->line('Laravel Version: ' . $metrics['laravel_version']);
        $this->newLine();
    }

    /**
     * Export metrics to file
     */
    private function exportMetrics(array $metrics): void
    {
        $filename = 'performance_metrics_' . now()->format('Y-m-d_H-i-s') . '.json';
        $filepath = storage_path('logs/' . $filename);

        file_put_contents($filepath, json_encode($metrics, JSON_PRETTY_PRINT));

        $this->info("Metrics exported to: {$filepath}");
    }

    /**
     * Helper methods for collecting specific metrics
     */
    private function getConnectionCount(): int
    {
        // Implementar lógica para contar conexões
        return 0;
    }

    private function getSlowQueries(): array
    {
        // Implementar lógica para obter consultas lentas
        return [];
    }

    private function getTableSizes(): array
    {
        if (config('database.default') === 'pgsql') {
            $sizes = DB::select("
                SELECT 
                    tablename,
                    pg_size_pretty(pg_total_relation_size(schemaname||'.'||tablename)) as size
                FROM pg_tables 
                WHERE schemaname = 'public'
                ORDER BY pg_total_relation_size(schemaname||'.'||tablename) DESC
            ");

            $result = [];
            foreach ($sizes as $size) {
                $result[$size->tablename] = $size->size;
            }
            return $result;
        }

        return [];
    }

    private function getIndexUsage(): array
    {
        // Implementar lógica para obter uso de índices
        return [];
    }

    private function getPostgresStats(): array
    {
        // Implementar estatísticas específicas do PostgreSQL
        return [];
    }

    private function getMysqlStats(): array
    {
        // Implementar estatísticas específicas do MySQL
        return [];
    }

    private function getRedisInfo(): array
    {
        if (config('cache.default') === 'redis') {
            return \Redis::info();
        }
        return [];
    }

    private function getCacheKeysCount(): int
    {
        if (config('cache.default') === 'redis') {
            return count(\Redis::keys('*'));
        }
        return 0;
    }

    private function getFileCacheSize(): string
    {
        $cachePath = storage_path('framework/cache');
        if (is_dir($cachePath)) {
            $size = $this->getDirectorySize($cachePath);
            return $this->formatBytes($size);
        }
        return '0 B';
    }

    private function getUptime(): string
    {
        if (function_exists('sys_getloadavg')) {
            $uptime = shell_exec('uptime');
            return trim($uptime);
        }
        return 'N/A';
    }

    private function getDirectorySize(string $directory): int
    {
        $size = 0;
        foreach (glob(rtrim($directory, '/') . '/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : $this->getDirectorySize($each);
        }
        return $size;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}