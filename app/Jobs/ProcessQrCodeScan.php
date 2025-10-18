<?php

namespace App\Jobs;

use App\Models\QrCode;
use App\Models\QrScan;
use App\Services\QrScanTracker;
use App\Services\CacheService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProcessQrCodeScan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected QrCode $qrCode;
    protected array $scanData;

    /**
     * Create a new job instance.
     */
    public function __construct(QrCode $qrCode, array $scanData)
    {
        $this->qrCode = $qrCode;
        $this->scanData = $scanData;
    }

    /**
     * Execute the job.
     */
    public function handle(QrScanTracker $scanTracker, CacheService $cacheService): void
    {
        try {
            DB::beginTransaction();

            // Verificar se é um scan único
            $isUnique = $this->isUniqueScan();

            // Criar registro de scan
            $scan = QrScan::create([
                'qr_code_id' => $this->qrCode->id,
                'ip_address' => $this->scanData['ip_address'] ?? null,
                'country' => $this->scanData['country'] ?? null,
                'city' => $this->scanData['city'] ?? null,
                'latitude' => $this->scanData['latitude'] ?? null,
                'longitude' => $this->scanData['longitude'] ?? null,
                'device_type' => $this->scanData['device_type'] ?? null,
                'browser' => $this->scanData['browser'] ?? null,
                'os' => $this->scanData['os'] ?? null,
                'is_unique' => $isUnique,
                'scanned_at' => now(),
            ]);

            // Atualizar contador de scans do QR Code
            $this->qrCode->increment('scans_count');

            // Se for scan único, incrementar contador de scans únicos
            if ($isUnique) {
                $this->qrCode->increment('unique_scans_count');
            }

            // Invalidar cache de estatísticas
            $cacheService->invalidateQrCodeStats($this->qrCode);
            $cacheService->invalidateUserStats($this->qrCode->user);

            // Incrementar contador em cache para tempo real
            $cacheService->incrementScanCount($this->qrCode);

            DB::commit();

            Log::info('QR Code scan processed successfully', [
                'qr_code_id' => $this->qrCode->id,
                'scan_id' => $scan->id,
                'is_unique' => $isUnique,
                'ip_address' => $this->scanData['ip_address'] ?? null,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to process QR Code scan', [
                'qr_code_id' => $this->qrCode->id,
                'scan_data' => $this->scanData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Verificar se é um scan único
     */
    private function isUniqueScan(): bool
    {
        $ipAddress = $this->scanData['ip_address'] ?? null;
        $deviceType = $this->scanData['device_type'] ?? null;
        $browser = $this->scanData['browser'] ?? null;

        if (!$ipAddress) {
            return false;
        }

        // Verificar se já houve scan deste IP/device nas últimas 24 horas
        $recentScan = QrScan::where('qr_code_id', $this->qrCode->id)
            ->where('ip_address', $ipAddress)
            ->where('scanned_at', '>=', now()->subHours(24))
            ->when($deviceType, function ($query, $deviceType) {
                return $query->where('device_type', $deviceType);
            })
            ->when($browser, function ($query, $browser) {
                return $query->where('browser', $browser);
            })
            ->exists();

        return !$recentScan;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('QR Code scan processing job failed', [
            'qr_code_id' => $this->qrCode->id,
            'scan_data' => $this->scanData,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}