<?php

namespace App\Jobs;

use App\Services\MercadoPagoService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessSubscriptionWebhook implements ShouldQueue
{
    use Queueable;

    protected array $webhookData;

    /**
     * Create a new job instance.
     */
    public function __construct(array $webhookData)
    {
        $this->webhookData = $webhookData;
    }

    /**
     * Execute the job.
     */
    public function handle(MercadoPagoService $mercadoPagoService): void
    {
        try {
            Log::info('Processando webhook Mercado Pago em background', $this->webhookData);
            
            $result = $mercadoPagoService->processWebhook($this->webhookData);
            
            if ($result) {
                Log::info('Webhook processado com sucesso');
            } else {
                Log::error('Falha ao processar webhook');
            }
            
        } catch (\Exception $e) {
            Log::error('Erro ao processar webhook em background: ' . $e->getMessage(), [
                'webhook_data' => $this->webhookData,
                'exception' => $e,
            ]);
            
            // Re-throw para que o job falhe e seja reprocessado
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job de webhook falhou definitivamente', [
            'webhook_data' => $this->webhookData,
            'exception' => $exception,
        ]);
    }
}
