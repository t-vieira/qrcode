<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SharedHostingService;
use Illuminate\Support\Facades\File;

class ConfigureSharedHosting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shared-hosting:configure {--check : Only check configuration without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configure the application for shared hosting environment';

    protected SharedHostingService $sharedHostingService;

    public function __construct(SharedHostingService $sharedHostingService)
    {
        parent::__construct();
        $this->sharedHostingService = $sharedHostingService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Configuring application for shared hosting...');
        $this->newLine();

        // Verificar limitações do servidor
        $this->checkServerLimitations();

        // Verificar suporte a funcionalidades
        $this->checkFeatureSupport();

        // Verificar espaço em disco
        $this->checkDiskSpace();

        if (!$this->option('check')) {
            // Configurar aplicação
            $this->configureApplication();

            // Otimizar configurações
            $this->optimizeConfiguration();

            // Criar diretórios necessários
            $this->createDirectories();

            // Configurar permissões
            $this->configurePermissions();

            // Limpar e otimizar
            $this->cleanupAndOptimize();
        }

        $this->newLine();
        $this->info('✅ Shared hosting configuration completed!');
    }

    /**
     * Verificar limitações do servidor
     */
    private function checkServerLimitations(): void
    {
        $this->info('📊 Checking server limitations...');

        $limitations = $this->sharedHostingService->checkServerLimitations();

        $this->table(
            ['Setting', 'Value', 'Status'],
            [
                ['Memory Limit', $limitations['memory_limit'], $this->getMemoryStatus($limitations['memory_limit'])],
                ['Max Execution Time', $limitations['max_execution_time'] . 's', $this->getExecutionTimeStatus($limitations['max_execution_time'])],
                ['Upload Max Filesize', $limitations['upload_max_filesize'], $this->getUploadSizeStatus($limitations['upload_max_filesize'])],
                ['Post Max Size', $limitations['post_max_size'], $this->getPostSizeStatus($limitations['post_max_size'])],
                ['Max Input Vars', $limitations['max_input_vars'], $this->getInputVarsStatus($limitations['max_input_vars'])],
                ['Max File Uploads', $limitations['max_file_uploads'], $this->getFileUploadsStatus($limitations['max_file_uploads'])],
            ]
        );

        $this->newLine();
    }

    /**
     * Verificar suporte a funcionalidades
     */
    private function checkFeatureSupport(): void
    {
        $this->info('🔍 Checking feature support...');

        $features = $this->sharedHostingService->checkFeatureSupport();

        $requiredFeatures = [
            'gd' => 'Image processing',
            'curl' => 'HTTP requests',
            'openssl' => 'SSL/TLS',
            'json' => 'JSON processing',
            'mbstring' => 'Multibyte strings',
            'xml' => 'XML processing',
            'pdo_pgsql' => 'PostgreSQL database',
            'fileinfo' => 'File type detection',
        ];

        $optionalFeatures = [
            'redis' => 'Redis cache',
            'memcached' => 'Memcached cache',
            'imagick' => 'ImageMagick',
            'zip' => 'ZIP compression',
            'exif' => 'EXIF data',
        ];

        $this->line('Required Features:');
        foreach ($requiredFeatures as $feature => $description) {
            $status = $features[$feature] ? '✅' : '❌';
            $this->line("  {$status} {$description} ({$feature})");
        }

        $this->newLine();
        $this->line('Optional Features:');
        foreach ($optionalFeatures as $feature => $description) {
            $status = $features[$feature] ? '✅' : '⚠️';
            $this->line("  {$status} {$description} ({$feature})");
        }

        $this->newLine();
    }

    /**
     * Verificar espaço em disco
     */
    private function checkDiskSpace(): void
    {
        $this->info('💾 Checking disk space...');

        $diskSpace = $this->sharedHostingService->checkDiskSpace();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Space', $this->formatBytes($diskSpace['total'])],
                ['Used Space', $this->formatBytes($diskSpace['used'])],
                ['Free Space', $this->formatBytes($diskSpace['free'])],
                ['Used Percentage', $diskSpace['percentage_used'] . '%'],
                ['Free Percentage', $diskSpace['percentage_free'] . '%'],
            ]
        );

        if ($diskSpace['percentage_used'] > 80) {
            $this->warn('⚠️  Disk space is running low!');
        }

        $this->newLine();
    }

    /**
     * Configurar aplicação
     */
    private function configureApplication(): void
    {
        $this->info('⚙️ Configuring application...');

        $this->sharedHostingService->configureForSharedHosting();

        $this->info('✅ Application configured for shared hosting');
    }

    /**
     * Otimizar configurações
     */
    private function optimizeConfiguration(): void
    {
        $this->info('🚀 Optimizing configuration...');

        // Configurar cache
        $cacheDriver = $this->sharedHostingService->configureCacheDriver();
        $this->line("  Cache driver: {$cacheDriver}");

        // Configurar queue
        $queueDriver = $this->sharedHostingService->configureQueueDriver();
        $this->line("  Queue driver: {$queueDriver}");

        // Configurar storage
        $this->line("  Storage driver: local");

        $this->info('✅ Configuration optimized');
    }

    /**
     * Criar diretórios necessários
     */
    private function createDirectories(): void
    {
        $this->info('📁 Creating necessary directories...');

        $directories = [
            'storage/app/public/qrcodes',
            'storage/app/public/logos',
            'storage/app/public/stickers',
            'storage/framework/cache',
            'storage/framework/sessions',
            'storage/framework/views',
            'storage/logs',
        ];

        foreach ($directories as $directory) {
            $path = storage_path($directory);
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
                $this->line("  Created: {$directory}");
            } else {
                $this->line("  Exists: {$directory}");
            }
        }

        $this->info('✅ Directories created');
    }

    /**
     * Configurar permissões
     */
    private function configurePermissions(): void
    {
        $this->info('🔐 Configuring permissions...');

        $paths = [
            'storage' => 0755,
            'bootstrap/cache' => 0755,
            'public' => 0755,
        ];

        foreach ($paths as $path => $permission) {
            $fullPath = base_path($path);
            if (is_dir($fullPath)) {
                chmod($fullPath, $permission);
                $this->line("  Set permissions for {$path}: " . decoct($permission));
            }
        }

        $this->info('✅ Permissions configured');
    }

    /**
     * Limpar e otimizar
     */
    private function cleanupAndOptimize(): void
    {
        $this->info('🧹 Cleaning up and optimizing...');

        $this->sharedHostingService->cleanupAndOptimize();

        $this->info('✅ Cleanup and optimization completed');
    }

    /**
     * Obter status da memória
     */
    private function getMemoryStatus(string $memoryLimit): string
    {
        $bytes = $this->parseSize($memoryLimit);
        if ($bytes >= 256 * 1024 * 1024) { // 256MB
            return '✅ Good';
        } elseif ($bytes >= 128 * 1024 * 1024) { // 128MB
            return '⚠️  Acceptable';
        } else {
            return '❌ Low';
        }
    }

    /**
     * Obter status do tempo de execução
     */
    private function getExecutionTimeStatus(int $maxTime): string
    {
        if ($maxTime >= 300) { // 5 minutos
            return '✅ Good';
        } elseif ($maxTime >= 120) { // 2 minutos
            return '⚠️  Acceptable';
        } else {
            return '❌ Low';
        }
    }

    /**
     * Obter status do tamanho de upload
     */
    private function getUploadSizeStatus(string $uploadSize): string
    {
        $bytes = $this->parseSize($uploadSize);
        if ($bytes >= 10 * 1024 * 1024) { // 10MB
            return '✅ Good';
        } elseif ($bytes >= 5 * 1024 * 1024) { // 5MB
            return '⚠️  Acceptable';
        } else {
            return '❌ Low';
        }
    }

    /**
     * Obter status do tamanho de POST
     */
    private function getPostSizeStatus(string $postSize): string
    {
        $bytes = $this->parseSize($postSize);
        if ($bytes >= 10 * 1024 * 1024) { // 10MB
            return '✅ Good';
        } elseif ($bytes >= 5 * 1024 * 1024) { // 5MB
            return '⚠️  Acceptable';
        } else {
            return '❌ Low';
        }
    }

    /**
     * Obter status das variáveis de entrada
     */
    private function getInputVarsStatus(int $maxVars): string
    {
        if ($maxVars >= 3000) {
            return '✅ Good';
        } elseif ($maxVars >= 1000) {
            return '⚠️  Acceptable';
        } else {
            return '❌ Low';
        }
    }

    /**
     * Obter status dos uploads de arquivo
     */
    private function getFileUploadsStatus(int $maxUploads): string
    {
        if ($maxUploads >= 20) {
            return '✅ Good';
        } elseif ($maxUploads >= 10) {
            return '⚠️  Acceptable';
        } else {
            return '❌ Low';
        }
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
     * Formatar bytes para formato legível
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}