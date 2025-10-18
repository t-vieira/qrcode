<?php

namespace App\Services;

use App\Jobs\ProcessSubscriptionWebhook;
use App\Jobs\ExpireTrialSubscriptions;
use App\Jobs\SendWhatsAppNotification;
use App\Jobs\GenerateQrCodeFile;
use App\Jobs\SendEmailNotification;
use App\Jobs\CleanupExpiredFiles;
use App\Jobs\ProcessQrCodeScan;
use App\Jobs\GenerateReport;
use App\Jobs\VerifyCustomDomain;
use App\Jobs\SendTrialExpiringNotification;
use App\Models\User;
use App\Models\QrCode;
use App\Models\QrScan;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;

class QueueService
{
    /**
     * Processar webhook de assinatura em fila
     */
    public function processSubscriptionWebhook(array $webhookData): void
    {
        ProcessSubscriptionWebhook::dispatch($webhookData)
            ->onQueue('webhooks')
            ->delay(now()->addSeconds(5)); // Pequeno delay para evitar processamento duplicado
    }

    /**
     * Expirar assinaturas de trial
     */
    public function expireTrialSubscriptions(): void
    {
        ExpireTrialSubscriptions::dispatch()
            ->onQueue('subscriptions')
            ->delay(now()->addMinutes(1));
    }

    /**
     * Enviar notificação via WhatsApp
     */
    public function sendWhatsAppNotification(string $phone, string $message, array $data = []): void
    {
        SendWhatsAppNotification::dispatch($phone, $message, $data)
            ->onQueue('notifications')
            ->delay(now()->addSeconds(10));
    }

    /**
     * Gerar arquivo de QR Code em fila
     */
    public function generateQrCodeFile(QrCode $qrCode, array $options = []): void
    {
        GenerateQrCodeFile::dispatch($qrCode, $options)
            ->onQueue('qrcodes')
            ->delay(now()->addSeconds(5));
    }

    /**
     * Enviar email de notificação
     */
    public function sendEmailNotification(User $user, string $type, array $data = []): void
    {
        SendEmailNotification::dispatch($user, $type, $data)
            ->onQueue('emails')
            ->delay(now()->addSeconds(5));
    }

    /**
     * Processar scan de QR Code em fila
     */
    public function processQrCodeScan(QrCode $qrCode, array $scanData): void
    {
        ProcessQrCodeScan::dispatch($qrCode, $scanData)
            ->onQueue('scans')
            ->delay(now()->addSeconds(2));
    }

    /**
     * Gerar relatório em fila
     */
    public function generateReport(User $user, string $type, array $options = []): void
    {
        GenerateReport::dispatch($user, $type, $options)
            ->onQueue('reports')
            ->delay(now()->addMinutes(1));
    }

    /**
     * Verificar domínio customizado
     */
    public function verifyCustomDomain(int $domainId): void
    {
        VerifyCustomDomain::dispatch($domainId)
            ->onQueue('domains')
            ->delay(now()->addMinutes(5));
    }

    /**
     * Enviar notificação de trial expirando
     */
    public function sendTrialExpiringNotification(User $user, int $daysLeft): void
    {
        SendTrialExpiringNotification::dispatch($user, $daysLeft)
            ->onQueue('notifications')
            ->delay(now()->addMinutes(1));
    }

    /**
     * Limpar arquivos expirados
     */
    public function cleanupExpiredFiles(): void
    {
        CleanupExpiredFiles::dispatch()
            ->onQueue('maintenance')
            ->delay(now()->addHours(1));
    }

    /**
     * Processar múltiplos scans em lote
     */
    public function processBatchScans(array $scansData): void
    {
        foreach ($scansData as $scanData) {
            $this->processQrCodeScan($scanData['qr_code'], $scanData);
        }
    }

    /**
     * Enviar notificações em lote
     */
    public function sendBatchNotifications(array $notifications): void
    {
        foreach ($notifications as $notification) {
            if ($notification['type'] === 'whatsapp') {
                $this->sendWhatsAppNotification(
                    $notification['phone'],
                    $notification['message'],
                    $notification['data'] ?? []
                );
            } elseif ($notification['type'] === 'email') {
                $this->sendEmailNotification(
                    $notification['user'],
                    $notification['template'],
                    $notification['data'] ?? []
                );
            }
        }
    }

    /**
     * Reprocessar jobs falhados
     */
    public function retryFailedJobs(string $queue = null, int $maxAttempts = 3): void
    {
        $query = \DB::table('failed_jobs');
        
        if ($queue) {
            $query->where('queue', $queue);
        }

        $failedJobs = $query->get();

        foreach ($failedJobs as $job) {
            $payload = json_decode($job->payload, true);
            $command = unserialize($payload['data']['command']);

            if ($command->attempts() < $maxAttempts) {
                $command->retry();
            } else {
                Log::error('Job failed after maximum attempts', [
                    'job_id' => $job->id,
                    'queue' => $job->queue,
                    'payload' => $payload,
                ]);
            }
        }
    }

    /**
     * Monitorar status das filas
     */
    public function getQueueStatus(): array
    {
        $queues = [
            'webhooks',
            'subscriptions',
            'notifications',
            'qrcodes',
            'emails',
            'scans',
            'reports',
            'domains',
            'maintenance',
        ];

        $status = [];

        foreach ($queues as $queue) {
            $status[$queue] = [
                'pending' => Queue::size($queue),
                'failed' => \DB::table('failed_jobs')->where('queue', $queue)->count(),
            ];
        }

        return $status;
    }

    /**
     * Limpar filas antigas
     */
    public function cleanupOldJobs(int $daysOld = 7): void
    {
        $cutoffDate = now()->subDays($daysOld);

        // Limpar jobs falhados antigos
        \DB::table('failed_jobs')
            ->where('failed_at', '<', $cutoffDate)
            ->delete();

        // Limpar jobs processados antigos (se usando banco de dados)
        if (config('queue.default') === 'database') {
            \DB::table('jobs')
                ->where('created_at', '<', $cutoffDate)
                ->delete();
        }

        Log::info('Cleaned up old queue jobs', ['cutoff_date' => $cutoffDate]);
    }

    /**
     * Pausar fila
     */
    public function pauseQueue(string $queue): void
    {
        // Implementar lógica para pausar fila
        // Isso depende do driver de fila usado
        Log::info("Queue paused: {$queue}");
    }

    /**
     * Retomar fila
     */
    public function resumeQueue(string $queue): void
    {
        // Implementar lógica para retomar fila
        Log::info("Queue resumed: {$queue}");
    }

    /**
     * Obter estatísticas de processamento
     */
    public function getProcessingStats(): array
    {
        return [
            'total_jobs_processed' => \DB::table('jobs')->count(),
            'total_failed_jobs' => \DB::table('failed_jobs')->count(),
            'average_processing_time' => $this->getAverageProcessingTime(),
            'queue_health' => $this->getQueueHealth(),
        ];
    }

    /**
     * Calcular tempo médio de processamento
     */
    private function getAverageProcessingTime(): float
    {
        // Implementar cálculo baseado nos logs ou métricas
        return 0.0;
    }

    /**
     * Verificar saúde das filas
     */
    private function getQueueHealth(): string
    {
        $failedJobs = \DB::table('failed_jobs')->count();
        $totalJobs = \DB::table('jobs')->count() + $failedJobs;

        if ($totalJobs === 0) {
            return 'healthy';
        }

        $failureRate = ($failedJobs / $totalJobs) * 100;

        if ($failureRate < 5) {
            return 'healthy';
        } elseif ($failureRate < 15) {
            return 'warning';
        } else {
            return 'critical';
        }
    }

    /**
     * Agendar jobs recorrentes
     */
    public function scheduleRecurringJobs(): void
    {
        // Expirar trials diariamente
        $this->expireTrialSubscriptions();

        // Limpar arquivos expirados semanalmente
        $this->cleanupExpiredFiles();

        // Limpar jobs antigos semanalmente
        $this->cleanupOldJobs();

        Log::info('Scheduled recurring jobs');
    }

    /**
     * Processar webhook de alta prioridade
     */
    public function processHighPriorityWebhook(array $webhookData): void
    {
        ProcessSubscriptionWebhook::dispatch($webhookData)
            ->onQueue('webhooks-high')
            ->delay(now()->addSeconds(1));
    }

    /**
     * Enviar notificação urgente
     */
    public function sendUrgentNotification(string $phone, string $message): void
    {
        SendWhatsAppNotification::dispatch($phone, $message, ['urgent' => true])
            ->onQueue('notifications-urgent')
            ->delay(now()->addSeconds(1));
    }
}
