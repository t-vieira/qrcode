<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Services\SharedHostingService;

class CleanupSharedHosting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shared-hosting:cleanup {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up files and optimize storage for shared hosting';

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
        $this->info('🧹 Starting shared hosting cleanup...');
        $this->newLine();

        // Verificar espaço em disco antes da limpeza
        $diskSpaceBefore = $this->sharedHostingService->checkDiskSpace();
        $this->info("💾 Disk space before cleanup: {$this->formatBytes($diskSpaceBefore['used'])} used, {$this->formatBytes($diskSpaceBefore['free'])} free");

        if (!$this->option('force')) {
            if (!$this->confirm('Do you want to proceed with cleanup?')) {
                $this->info('Cleanup cancelled.');
                return;
            }
        }

        // Limpar arquivos antigos
        $this->cleanupOldFiles();

        // Limpar cache
        $this->cleanupCache();

        // Limpar logs
        $this->cleanupLogs();

        // Limpar sessões
        $this->cleanupSessions();

        // Limpar QR Codes antigos
        $this->cleanupOldQrCodes();

        // Limpar arquivos temporários
        $this->cleanupTempFiles();

        // Otimizar storage
        $this->optimizeStorage();

        // Verificar espaço em disco após a limpeza
        $diskSpaceAfter = $this->sharedHostingService->checkDiskSpace();
        $freedSpace = $diskSpaceBefore['used'] - $diskSpaceAfter['used'];

        $this->newLine();
        $this->info("✅ Cleanup completed!");
        $this->info("💾 Disk space after cleanup: {$this->formatBytes($diskSpaceAfter['used'])} used, {$this->formatBytes($diskSpaceAfter['free'])} free");
        $this->info("🗑️  Freed space: {$this->formatBytes($freedSpace)}");
    }

    /**
     * Limpar arquivos antigos
     */
    private function cleanupOldFiles(): void
    {
        $this->info('📁 Cleaning up old files...');

        $directories = [
            'storage/framework/cache' => 7, // 7 dias
            'storage/framework/views' => 7, // 7 dias
            'storage/app/temp' => 1, // 1 dia
        ];

        $totalFiles = 0;
        $totalSize = 0;

        foreach ($directories as $directory => $days) {
            $path = storage_path($directory);
            if (is_dir($path)) {
                $files = glob($path . '/*');
                $cutoff = time() - ($days * 24 * 60 * 60);

                foreach ($files as $file) {
                    if (is_file($file) && filemtime($file) < $cutoff) {
                        $fileSize = filesize($file);
                        unlink($file);
                        $totalFiles++;
                        $totalSize += $fileSize;
                    }
                }
            }
        }

        $this->line("  Removed {$totalFiles} files, freed {$this->formatBytes($totalSize)}");
    }

    /**
     * Limpar cache
     */
    private function cleanupCache(): void
    {
        $this->info('🗂️  Cleaning up cache...');

        $cacheDirectories = [
            'storage/framework/cache/data',
            'storage/framework/cache/views',
            'bootstrap/cache',
        ];

        $totalFiles = 0;
        $totalSize = 0;

        foreach ($cacheDirectories as $directory) {
            $path = base_path($directory);
            if (is_dir($path)) {
                $files = glob($path . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $fileSize = filesize($file);
                        unlink($file);
                        $totalFiles++;
                        $totalSize += $fileSize;
                    }
                }
            }
        }

        $this->line("  Removed {$totalFiles} cache files, freed {$this->formatBytes($totalSize)}");
    }

    /**
     * Limpar logs
     */
    private function cleanupLogs(): void
    {
        $this->info('📝 Cleaning up logs...');

        $logPath = storage_path('logs');
        if (is_dir($logPath)) {
            $files = glob($logPath . '/*.log');
            $cutoff = time() - (7 * 24 * 60 * 60); // 7 dias

            $totalFiles = 0;
            $totalSize = 0;

            foreach ($files as $file) {
                if (is_file($file) && filemtime($file) < $cutoff) {
                    $fileSize = filesize($file);
                    unlink($file);
                    $totalFiles++;
                    $totalSize += $fileSize;
                }
            }

            $this->line("  Removed {$totalFiles} log files, freed {$this->formatBytes($totalSize)}");
        }
    }

    /**
     * Limpar sessões
     */
    private function cleanupSessions(): void
    {
        $this->info('🔐 Cleaning up sessions...');

        $sessionPath = storage_path('framework/sessions');
        if (is_dir($sessionPath)) {
            $files = glob($sessionPath . '/sess_*');
            $cutoff = time() - (24 * 60 * 60); // 1 dia

            $totalFiles = 0;
            $totalSize = 0;

            foreach ($files as $file) {
                if (is_file($file) && filemtime($file) < $cutoff) {
                    $fileSize = filesize($file);
                    unlink($file);
                    $totalFiles++;
                    $totalSize += $fileSize;
                }
            }

            $this->line("  Removed {$totalFiles} session files, freed {$this->formatBytes($totalSize)}");
        }
    }

    /**
     * Limpar QR Codes antigos
     */
    private function cleanupOldQrCodes(): void
    {
        $this->info('📱 Cleaning up old QR codes...');

        $qrCodePath = storage_path('app/public/qrcodes');
        if (is_dir($qrCodePath)) {
            $files = glob($qrCodePath . '/**/*');
            $cutoff = time() - (30 * 24 * 60 * 60); // 30 dias

            $totalFiles = 0;
            $totalSize = 0;

            foreach ($files as $file) {
                if (is_file($file) && filemtime($file) < $cutoff) {
                    $fileSize = filesize($file);
                    unlink($file);
                    $totalFiles++;
                    $totalSize += $fileSize;
                }
            }

            $this->line("  Removed {$totalFiles} QR code files, freed {$this->formatBytes($totalSize)}");
        }
    }

    /**
     * Limpar arquivos temporários
     */
    private function cleanupTempFiles(): void
    {
        $this->info('🗑️  Cleaning up temporary files...');

        $tempDirectories = [
            'storage/app/temp',
            'storage/app/public/temp',
            'tmp',
        ];

        $totalFiles = 0;
        $totalSize = 0;

        foreach ($tempDirectories as $directory) {
            $path = storage_path($directory);
            if (is_dir($path)) {
                $files = glob($path . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $fileSize = filesize($file);
                        unlink($file);
                        $totalFiles++;
                        $totalSize += $fileSize;
                    }
                }
            }
        }

        $this->line("  Removed {$totalFiles} temporary files, freed {$this->formatBytes($totalSize)}");
    }

    /**
     * Otimizar storage
     */
    private function optimizeStorage(): void
    {
        $this->info('⚡ Optimizing storage...');

        // Otimizar autoloader
        if (function_exists('opcache_compile_file')) {
            $autoloadFile = base_path('vendor/autoload.php');
            if (file_exists($autoloadFile)) {
                opcache_compile_file($autoloadFile);
                $this->line("  Optimized autoloader");
            }
        }

        // Limpar diretórios vazios
        $this->removeEmptyDirectories();

        $this->line("  Storage optimization completed");
    }

    /**
     * Remover diretórios vazios
     */
    private function removeEmptyDirectories(): void
    {
        $directories = [
            'storage/app/public/qrcodes',
            'storage/app/public/logos',
            'storage/app/public/stickers',
        ];

        $removedDirs = 0;

        foreach ($directories as $directory) {
            $path = storage_path($directory);
            if (is_dir($path)) {
                $this->removeEmptyDir($path, $removedDirs);
            }
        }

        if ($removedDirs > 0) {
            $this->line("  Removed {$removedDirs} empty directories");
        }
    }

    /**
     * Remover diretório vazio recursivamente
     */
    private function removeEmptyDir(string $dir, int &$count): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = scandir($dir);
        $files = array_diff($files, ['.', '..']);

        if (empty($files)) {
            rmdir($dir);
            $count++;
        } else {
            foreach ($files as $file) {
                $filePath = $dir . '/' . $file;
                if (is_dir($filePath)) {
                    $this->removeEmptyDir($filePath, $count);
                }
            }

            // Verificar novamente se o diretório está vazio
            $files = scandir($dir);
            $files = array_diff($files, ['.', '..']);
            if (empty($files)) {
                rmdir($dir);
                $count++;
            }
        }
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