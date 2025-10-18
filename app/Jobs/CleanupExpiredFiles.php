<?php

namespace App\Jobs;

use App\Models\QrCode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupExpiredFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->cleanupQrCodeFiles();
            $this->cleanupTempFiles();
            $this->cleanupLogFiles();
            $this->cleanupCacheFiles();

            Log::info('File cleanup completed successfully');

        } catch (\Exception $e) {
            Log::error('File cleanup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Limpar arquivos de QR Codes antigos
     */
    private function cleanupQrCodeFiles(): void
    {
        $cutoffDate = now()->subDays(30); // Manter arquivos por 30 dias
        
        // Buscar QR Codes deletados há mais de 30 dias
        $deletedQrCodes = QrCode::onlyTrashed()
            ->where('deleted_at', '<', $cutoffDate)
            ->whereNotNull('file_path')
            ->get();

        $deletedCount = 0;
        $totalSize = 0;

        foreach ($deletedQrCodes as $qrCode) {
            if ($this->deleteQrCodeFile($qrCode->file_path)) {
                $deletedCount++;
                $totalSize += $this->getFileSize($qrCode->file_path);
            }
        }

        Log::info('QR Code files cleanup completed', [
            'deleted_files' => $deletedCount,
            'total_size_freed' => $this->formatBytes($totalSize)
        ]);
    }

    /**
     * Limpar arquivos temporários
     */
    private function cleanupTempFiles(): void
    {
        $tempDirectories = [
            'temp',
            'uploads/temp',
            'exports/temp',
        ];

        $deletedCount = 0;
        $totalSize = 0;

        foreach ($tempDirectories as $directory) {
            if (Storage::disk('public')->exists($directory)) {
                $files = Storage::disk('public')->files($directory);
                
                foreach ($files as $file) {
                    $fileTime = Storage::disk('public')->lastModified($file);
                    $fileDate = Carbon::createFromTimestamp($fileTime);
                    
                    // Deletar arquivos temporários com mais de 24 horas
                    if ($fileDate->isBefore(now()->subHours(24))) {
                        $fileSize = Storage::disk('public')->size($file);
                        if (Storage::disk('public')->delete($file)) {
                            $deletedCount++;
                            $totalSize += $fileSize;
                        }
                    }
                }
            }
        }

        Log::info('Temp files cleanup completed', [
            'deleted_files' => $deletedCount,
            'total_size_freed' => $this->formatBytes($totalSize)
        ]);
    }

    /**
     * Limpar arquivos de log antigos
     */
    private function cleanupLogFiles(): void
    {
        $logPath = storage_path('logs');
        
        if (!is_dir($logPath)) {
            return;
        }

        $files = glob($logPath . '/*.log');
        $deletedCount = 0;
        $totalSize = 0;

        foreach ($files as $file) {
            $fileTime = filemtime($file);
            $fileDate = Carbon::createFromTimestamp($fileTime);
            
            // Deletar logs com mais de 30 dias
            if ($fileDate->isBefore(now()->subDays(30))) {
                $fileSize = filesize($file);
                if (unlink($file)) {
                    $deletedCount++;
                    $totalSize += $fileSize;
                }
            }
        }

        Log::info('Log files cleanup completed', [
            'deleted_files' => $deletedCount,
            'total_size_freed' => $this->formatBytes($totalSize)
        ]);
    }

    /**
     * Limpar arquivos de cache antigos
     */
    private function cleanupCacheFiles(): void
    {
        $cachePath = storage_path('framework/cache');
        
        if (!is_dir($cachePath)) {
            return;
        }

        $files = glob($cachePath . '/data/*');
        $deletedCount = 0;
        $totalSize = 0;

        foreach ($files as $file) {
            if (is_file($file)) {
                $fileTime = filemtime($file);
                $fileDate = Carbon::createFromTimestamp($fileTime);
                
                // Deletar cache com mais de 7 dias
                if ($fileDate->isBefore(now()->subDays(7))) {
                    $fileSize = filesize($file);
                    if (unlink($file)) {
                        $deletedCount++;
                        $totalSize += $fileSize;
                    }
                }
            }
        }

        Log::info('Cache files cleanup completed', [
            'deleted_files' => $deletedCount,
            'total_size_freed' => $this->formatBytes($totalSize)
        ]);
    }

    /**
     * Deletar arquivo de QR Code
     */
    private function deleteQrCodeFile(string $filePath): bool
    {
        try {
            if (Storage::disk('public')->exists($filePath)) {
                return Storage::disk('public')->delete($filePath);
            }
            return true;
        } catch (\Exception $e) {
            Log::warning('Failed to delete QR Code file', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Obter tamanho do arquivo
     */
    private function getFileSize(string $filePath): int
    {
        try {
            if (Storage::disk('public')->exists($filePath)) {
                return Storage::disk('public')->size($filePath);
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Formatar bytes em formato legível
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('File cleanup job failed', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}