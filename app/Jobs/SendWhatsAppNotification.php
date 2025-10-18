<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendWhatsAppNotification implements ShouldQueue
{
    use Queueable;

    protected User $user;
    protected string $type;
    protected array $data;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $type, array $data = [])
    {
        $this->user = $user;
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsAppService): void
    {
        try {
            // Verificar se o WhatsApp está configurado
            if (!$whatsAppService->isConfigured()) {
                Log::warning('WhatsApp não configurado, pulando notificação', [
                    'user_id' => $this->user->id,
                    'type' => $this->type,
                ]);
                return;
            }

            // Verificar se o usuário tem telefone
            if (empty($this->user->phone)) {
                Log::info('Usuário sem telefone, pulando notificação WhatsApp', [
                    'user_id' => $this->user->id,
                    'type' => $this->type,
                ]);
                return;
            }

            $result = match ($this->type) {
                'welcome' => $this->sendWelcomeNotification($whatsAppService),
                'subscription_activated' => $this->sendSubscriptionActivatedNotification($whatsAppService),
                'trial_expiring' => $this->sendTrialExpiringNotification($whatsAppService),
                'support_response' => $this->sendSupportResponseNotification($whatsAppService),
                default => null,
            };

            if ($result && $result['success']) {
                Log::info("Notificação WhatsApp enviada com sucesso", [
                    'user_id' => $this->user->id,
                    'type' => $this->type,
                    'message_id' => $result['message_id'] ?? null,
                ]);
            } else {
                Log::error("Falha ao enviar notificação WhatsApp", [
                    'user_id' => $this->user->id,
                    'type' => $this->type,
                    'error' => $result['error'] ?? 'Erro desconhecido',
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Erro ao enviar notificação WhatsApp: ' . $e->getMessage(), [
                'user_id' => $this->user->id,
                'type' => $this->type,
                'exception' => $e,
            ]);
            
            // Re-throw para que o job falhe e seja reprocessado
            throw $e;
        }
    }

    /**
     * Enviar notificação de boas-vindas
     */
    protected function sendWelcomeNotification(WhatsAppService $whatsAppService): array
    {
        if (!config('whatsapp.notifications.welcome.enabled')) {
            return ['success' => false, 'error' => 'Notificação de boas-vindas desabilitada'];
        }

        return $whatsAppService->sendWelcomeMessage($this->user);
    }

    /**
     * Enviar notificação de assinatura ativada
     */
    protected function sendSubscriptionActivatedNotification(WhatsAppService $whatsAppService): array
    {
        if (!config('whatsapp.notifications.subscription_activated.enabled')) {
            return ['success' => false, 'error' => 'Notificação de assinatura ativada desabilitada'];
        }

        return $whatsAppService->sendSubscriptionActivatedMessage($this->user);
    }

    /**
     * Enviar notificação de trial expirando
     */
    protected function sendTrialExpiringNotification(WhatsAppService $whatsAppService): array
    {
        if (!config('whatsapp.notifications.trial_expiring.enabled')) {
            return ['success' => false, 'error' => 'Notificação de trial expirando desabilitada'];
        }

        $daysLeft = $this->data['days_left'] ?? 1;
        return $whatsAppService->sendTrialExpiringMessage($this->user, $daysLeft);
    }

    /**
     * Enviar notificação de resposta de suporte
     */
    protected function sendSupportResponseNotification(WhatsAppService $whatsAppService): array
    {
        $ticketId = $this->data['ticket_id'] ?? null;
        $response = $this->data['response'] ?? '';

        if (!$ticketId || !$response) {
            return ['success' => false, 'error' => 'Dados insuficientes para notificação de suporte'];
        }

        $message = "📞 *Resposta do Suporte*\n\n";
        $message .= "Ticket #{$ticketId}\n\n";
        $message .= $response . "\n\n";
        $message .= "Se precisar de mais ajuda, responda esta mensagem.\n\n";
        $message .= "Equipe QR Code SaaS";

        return $whatsAppService->sendMessage($this->user->phone, $message);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job de notificação WhatsApp falhou definitivamente', [
            'user_id' => $this->user->id,
            'type' => $this->type,
            'exception' => $exception,
        ]);
    }
}