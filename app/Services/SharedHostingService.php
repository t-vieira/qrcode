<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SharedHostingService
{
    /**
     * Verificar limitações do servidor compartilhado
     */
    public function checkServerLimitations(): array
    {
        $limitations = [
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_input_vars' => ini_get('max_input_vars'),
            'max_file_uploads' => ini_get('max_file_uploads'),
            'disk_free_space' => disk_free_space(base_path()),
            'disk_total_space' => disk_total_space(base_path()),
        ];

        return $limitations;
    }

    /**
     * Otimizar configurações para servidor compartilhado
     */
    public function optimizeForSharedHosting(): void
    {
        // Configurar cache para file driver
        config(['cache.default' => 'file']);
        config(['session.driver' => 'file']);
        config(['queue.default' => 'sync']);

        // Otimizar configurações de upload
        $this->optimizeUploadSettings();

        // Configurar logs para arquivo
        $this->configureFileLogging();

        // Otimizar storage
        $this->optimizeStorage();
    }

    /**
     * Otimizar configurações de upload
     */
    private function optimizeUploadSettings(): void
    {
        $maxFileSize = $this->getMaxFileSize();
        $maxExecutionTime = $this->getMaxExecutionTime();

        // Configurar limites baseados no servidor
        config([
            'qrcode.max_file_size' => $maxFileSize,
            'qrcode.max_resolution' => 1500, // Reduzido para servidor compartilhado
            'qrcode.max_execution_time' => $maxExecutionTime,
        ]);
    }

    /**
     * Configurar logging para arquivo
     */
    private function configureFileLogging(): void
    {
        config([
            'logging.default' => 'daily',
            'logging.channels.daily.path' => storage_path('logs/laravel.log'),
            'logging.channels.daily.days' => 7, // Reduzido para economizar espaço
        ]);
    }

    /**
     * Otimizar storage
     */
    private function optimizeStorage(): void
    {
        // Configurar storage local
        config([
            'filesystems.default' => 'local',
            'filesystems.disks.local.root' => storage_path('app'),
        ]);

        // Criar diretórios necessários
        $this->createStorageDirectories();
    }

    /**
     * Criar diretórios de storage necessários
     */
    private function createStorageDirectories(): void
    {
        $directories = [
            'app/public/qrcodes',
            'app/public/logos',
            'app/public/stickers',
            'framework/cache',
            'framework/sessions',
            'framework/views',
            'logs',
        ];

        foreach ($directories as $directory) {
            $path = storage_path($directory);
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }
    }

    /**
     * Obter tamanho máximo de arquivo permitido
     */
    private function getMaxFileSize(): int
    {
        $uploadMax = $this->parseSize(ini_get('upload_max_filesize'));
        $postMax = $this->parseSize(ini_get('post_max_size'));
        $memoryLimit = $this->parseSize(ini_get('memory_limit'));

        return min($uploadMax, $postMax, $memoryLimit / 4); // Usar 1/4 da memória
    }

    /**
     * Obter tempo máximo de execução
     */
    private function getMaxExecutionTime(): int
    {
        $maxTime = ini_get('max_execution_time');
        return $maxTime > 0 ? min($maxTime, 300) : 300; // Máximo 5 minutos
    }

    /**
     * Converter string de tamanho para bytes
     */
    private function parseSize(string $size): int
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $size = preg_replace('/[^0-9\.]/', '', $size);

        if ($unit) {
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }

        return round($size);
    }

    /**
     * Verificar se o servidor suporta funcionalidades específicas
     */
    public function checkFeatureSupport(): array
    {
        return [
            'redis' => extension_loaded('redis'),
            'memcached' => extension_loaded('memcached'),
            'gd' => extension_loaded('gd'),
            'imagick' => extension_loaded('imagick'),
            'curl' => extension_loaded('curl'),
            'openssl' => extension_loaded('openssl'),
            'zip' => extension_loaded('zip'),
            'json' => extension_loaded('json'),
            'mbstring' => extension_loaded('mbstring'),
            'xml' => extension_loaded('xml'),
            'pdo_pgsql' => extension_loaded('pdo_pgsql'),
            'pdo_mysql' => extension_loaded('pdo_mysql'),
            'fileinfo' => extension_loaded('fileinfo'),
            'exif' => extension_loaded('exif'),
        ];
    }

    /**
     * Configurar cache baseado nas extensões disponíveis
     */
    public function configureCacheDriver(): string
    {
        $features = $this->checkFeatureSupport();

        if ($features['redis']) {
            return 'redis';
        } elseif ($features['memcached']) {
            return 'memcached';
        } else {
            return 'file';
        }
    }

    /**
     * Configurar queue driver baseado nas extensões disponíveis
     */
    public function configureQueueDriver(): string
    {
        $features = $this->checkFeatureSupport();

        if ($features['redis']) {
            return 'redis';
        } else {
            return 'sync'; // Usar sync em servidor compartilhado
        }
    }

    /**
     * Limpar cache e otimizar para servidor compartilhado
     */
    public function cleanupAndOptimize(): void
    {
        // Limpar cache de arquivos antigos
        $this->cleanupOldCacheFiles();

        // Limpar logs antigos
        $this->cleanupOldLogs();

        // Otimizar autoloader
        $this->optimizeAutoloader();

        // Limpar sessões antigas
        $this->cleanupOldSessions();
    }

    /**
     * Limpar arquivos de cache antigos
     */
    private function cleanupOldCacheFiles(): void
    {
        $cachePath = storage_path('framework/cache');
        if (is_dir($cachePath)) {
            $files = glob($cachePath . '/*');
            $cutoff = time() - (7 * 24 * 60 * 60); // 7 dias

            foreach ($files as $file) {
                if (is_file($file) && filemtime($file) < $cutoff) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Limpar logs antigos
     */
    private function cleanupOldLogs(): void
    {
        $logPath = storage_path('logs');
        if (is_dir($logPath)) {
            $files = glob($logPath . '/*.log');
            $cutoff = time() - (7 * 24 * 60 * 60); // 7 dias

            foreach ($files as $file) {
                if (is_file($file) && filemtime($file) < $cutoff) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Otimizar autoloader
     */
    private function optimizeAutoloader(): void
    {
        if (function_exists('opcache_compile_file')) {
            $autoloadFile = base_path('vendor/autoload.php');
            if (file_exists($autoloadFile)) {
                opcache_compile_file($autoloadFile);
            }
        }
    }

    /**
     * Limpar sessões antigas
     */
    private function cleanupOldSessions(): void
    {
        $sessionPath = storage_path('framework/sessions');
        if (is_dir($sessionPath)) {
            $files = glob($sessionPath . '/sess_*');
            $cutoff = time() - (24 * 60 * 60); // 1 dia

            foreach ($files as $file) {
                if (is_file($file) && filemtime($file) < $cutoff) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Verificar espaço em disco
     */
    public function checkDiskSpace(): array
    {
        $totalSpace = disk_total_space(base_path());
        $freeSpace = disk_free_space(base_path());
        $usedSpace = $totalSpace - $freeSpace;

        return [
            'total' => $totalSpace,
            'free' => $freeSpace,
            'used' => $usedSpace,
            'percentage_used' => round(($usedSpace / $totalSpace) * 100, 2),
            'percentage_free' => round(($freeSpace / $totalSpace) * 100, 2),
        ];
    }

    /**
     * Verificar se há espaço suficiente para operação
     */
    public function hasEnoughSpace(int $requiredSpace = 10485760): bool // 10MB padrão
    {
        $diskSpace = $this->checkDiskSpace();
        return $diskSpace['free'] > $requiredSpace;
    }

    /**
     * Obter informações do servidor
     */
    public function getServerInfo(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
            'server_name' => $_SERVER['SERVER_NAME'] ?? 'Unknown',
            'server_addr' => $_SERVER['SERVER_ADDR'] ?? 'Unknown',
            'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? 'Unknown',
            'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'Unknown',
            'https' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'server_port' => $_SERVER['SERVER_PORT'] ?? 'Unknown',
        ];
    }

    /**
     * Configurar aplicação para servidor compartilhado
     */
    public function configureForSharedHosting(): void
    {
        // Otimizar configurações
        $this->optimizeForSharedHosting();

        // Configurar drivers baseados nas extensões disponíveis
        config([
            'cache.default' => $this->configureCacheDriver(),
            'queue.default' => $this->configureQueueDriver(),
        ]);

        // Log da configuração
        Log::info('Application configured for shared hosting', [
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'features' => $this->checkFeatureSupport(),
            'limitations' => $this->checkServerLimitations(),
        ]);
    }
}
