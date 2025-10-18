<?php

namespace App\Services;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $apiUrl;
    protected string $accessToken;
    protected string $phoneNumberId;
    protected string $businessAccountId;

    public function __construct()
    {
        $this->apiUrl = config('whatsapp.api_url', 'https://graph.facebook.com/v18.0');
        $this->accessToken = config('whatsapp.access_token');
        $this->phoneNumberId = config('whatsapp.phone_number_id');
        $this->businessAccountId = config('whatsapp.business_account_id');
    }

    /**
     * Enviar mensagem de suporte via WhatsApp
     */
    public function sendSupportMessage(User $user, string $message, string $priority = 'normal'): array
    {
        try {
            // Criar ticket de suporte
            $ticket = SupportTicket::create([
                'user_id' => $user->id,
                'whatsapp_number' => $user->phone ?? null,
                'message' => $message,
                'status' => 'open',
                'priority' => $priority,
            ]);

            // Formatar mensagem para o suporte
            $formattedMessage = $this->formatSupportMessage($user, $message, $ticket);

            // Enviar para o número de suporte
            $result = $this->sendMessage(
                config('whatsapp.support_number'),
                $formattedMessage
            );

            if ($result['success']) {
                Log::info("Mensagem de suporte enviada via WhatsApp", [
                    'user_id' => $user->id,
                    'ticket_id' => $ticket->id,
                    'message_id' => $result['message_id'] ?? null,
                ]);

                return [
                    'success' => true,
                    'ticket_id' => $ticket->id,
                    'message_id' => $result['message_id'] ?? null,
                ];
            }

            // Se falhou, marcar ticket como erro
            $ticket->update(['status' => 'error']);

            return [
                'success' => false,
                'error' => $result['error'] ?? 'Erro ao enviar mensagem',
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao enviar mensagem de suporte via WhatsApp: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'message' => $message,
            ]);

            return [
                'success' => false,
                'error' => 'Erro interno do servidor',
            ];
        }
    }

    /**
     * Enviar mensagem simples
     */
    public function sendMessage(string $to, string $message): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $this->formatPhoneNumber($to),
                'type' => 'text',
                'text' => [
                    'body' => $message,
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message_id' => $data['messages'][0]['id'] ?? null,
                    'data' => $data,
                ];
            }

            Log::error('Erro na API do WhatsApp', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'Erro na API do WhatsApp: ' . $response->status(),
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao enviar mensagem WhatsApp: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'Erro ao conectar com WhatsApp',
            ];
        }
    }

    /**
     * Enviar mensagem com template
     */
    public function sendTemplateMessage(string $to, string $templateName, array $parameters = []): array
    {
        try {
            $templateData = [
                'messaging_product' => 'whatsapp',
                'to' => $this->formatPhoneNumber($to),
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => [
                        'code' => 'pt_BR',
                    ],
                ],
            ];

            // Adicionar parâmetros se existirem
            if (!empty($parameters)) {
                $templateData['template']['components'] = [
                    [
                        'type' => 'body',
                        'parameters' => array_map(function ($param) {
                            return ['type' => 'text', 'text' => $param];
                        }, $parameters),
                    ],
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", $templateData);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message_id' => $data['messages'][0]['id'] ?? null,
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'error' => 'Erro na API do WhatsApp: ' . $response->status(),
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao enviar template WhatsApp: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'Erro ao conectar com WhatsApp',
            ];
        }
    }

    /**
     * Enviar notificação de boas-vindas
     */
    public function sendWelcomeMessage(User $user): array
    {
        $message = "🎉 *Bem-vindo ao QR Code SaaS!*\n\n";
        $message .= "Olá {$user->name}! Sua conta foi criada com sucesso.\n\n";
        $message .= "📱 *Seu período de teste:* 7 dias grátis\n";
        $message .= "🚀 *Funcionalidades disponíveis:*\n";
        $message .= "• QR Codes ilimitados\n";
        $message .= "• Personalização visual\n";
        $message .= "• Estatísticas básicas\n\n";
        $message .= "💡 *Dica:* Acesse seu dashboard para começar a criar QR Codes!\n\n";
        $message .= "Precisa de ajuda? Responda esta mensagem ou acesse nosso suporte.\n\n";
        $message .= "Atenciosamente,\nEquipe QR Code SaaS";

        return $this->sendMessage($user->phone ?? '', $message);
    }

    /**
     * Enviar notificação de assinatura ativada
     */
    public function sendSubscriptionActivatedMessage(User $user): array
    {
        $message = "✅ *Assinatura Ativada!*\n\n";
        $message .= "Parabéns {$user->name}! Sua assinatura premium foi ativada.\n\n";
        $message .= "🎯 *Agora você tem acesso a:*\n";
        $message .= "• QR Codes dinâmicos\n";
        $message .= "• Estatísticas avançadas\n";
        $message .= "• Domínios customizados\n";
        $message .= "• Sistema de equipes\n";
        $message .= "• Suporte prioritário\n\n";
        $message .= "Aproveite todas as funcionalidades!\n\n";
        $message .= "Equipe QR Code SaaS";

        return $this->sendMessage($user->phone ?? '', $message);
    }

    /**
     * Enviar notificação de trial expirando
     */
    public function sendTrialExpiringMessage(User $user, int $daysLeft): array
    {
        $message = "⏰ *Trial Expirando*\n\n";
        $message .= "Olá {$user->name}!\n\n";
        $message .= "Seu período de teste expira em {$daysLeft} dia(s).\n\n";
        $message .= "🔒 *Após a expiração:*\n";
        $message .= "• QR Codes dinâmicos serão desabilitados\n";
        $message .= "• Estatísticas avançadas ficarão limitadas\n";
        $message .= "• QR Codes estáticos continuarão funcionando\n\n";
        $message .= "💎 *Faça upgrade agora* e mantenha todas as funcionalidades!\n\n";
        $message .= "Acesse: " . route('subscription.upgrade') . "\n\n";
        $message .= "Equipe QR Code SaaS";

        return $this->sendMessage($user->phone ?? '', $message);
    }

    /**
     * Verificar status do webhook
     */
    public function verifyWebhook(string $token, string $challenge): ?string
    {
        $verifyToken = config('whatsapp.webhook_verify_token');
        
        if ($token === $verifyToken) {
            return $challenge;
        }

        return null;
    }

    /**
     * Processar webhook do WhatsApp
     */
    public function processWebhook(array $data): bool
    {
        try {
            if (!isset($data['entry'][0]['changes'][0]['value']['messages'])) {
                return true; // Não é uma mensagem
            }

            $messages = $data['entry'][0]['changes'][0]['value']['messages'];

            foreach ($messages as $message) {
                $this->processIncomingMessage($message);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Erro ao processar webhook WhatsApp: ' . $e->getMessage(), [
                'data' => $data,
            ]);

            return false;
        }
    }

    /**
     * Processar mensagem recebida
     */
    protected function processIncomingMessage(array $message): void
    {
        $from = $message['from'];
        $text = $message['text']['body'] ?? '';
        $messageId = $message['id'];

        // Verificar se é uma resposta a um ticket de suporte
        $ticket = SupportTicket::where('whatsapp_number', $from)
            ->where('status', 'open')
            ->latest()
            ->first();

        if ($ticket) {
            // Adicionar resposta ao ticket
            $ticket->update([
                'last_reply_at' => now(),
            ]);

            Log::info("Resposta recebida para ticket de suporte", [
                'ticket_id' => $ticket->id,
                'from' => $from,
                'message' => $text,
            ]);
        }
    }

    /**
     * Formatar mensagem de suporte
     */
    protected function formatSupportMessage(User $user, string $message, SupportTicket $ticket): string
    {
        $formattedMessage = "🆘 *NOVO TICKET DE SUPORTE*\n\n";
        $formattedMessage .= "📋 *Ticket ID:* #{$ticket->id}\n";
        $formattedMessage .= "👤 *Usuário:* {$user->name}\n";
        $formattedMessage .= "📧 *Email:* {$user->email}\n";
        $formattedMessage .= "📱 *Telefone:* " . ($user->phone ?? 'Não informado') . "\n";
        $formattedMessage .= "💳 *Status:* " . ucfirst($user->subscription_status) . "\n";
        $formattedMessage .= "📅 *Criado em:* " . $user->created_at->format('d/m/Y H:i') . "\n\n";
        $formattedMessage .= "💬 *Mensagem:*\n";
        $formattedMessage .= $message . "\n\n";
        $formattedMessage .= "⏰ *Recebido em:* " . now()->format('d/m/Y H:i:s');

        return $formattedMessage;
    }

    /**
     * Formatar número de telefone
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remover caracteres não numéricos
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Adicionar código do país se não tiver
        if (!str_starts_with($phone, '55')) {
            $phone = '55' . $phone;
        }

        return $phone;
    }

    /**
     * Verificar se o serviço está configurado
     */
    public function isConfigured(): bool
    {
        return !empty($this->accessToken) && 
               !empty($this->phoneNumberId) && 
               !empty($this->businessAccountId);
    }

    /**
     * Obter informações da conta
     */
    public function getAccountInfo(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
            ])->get("{$this->apiUrl}/{$this->businessAccountId}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => 'Erro ao obter informações da conta',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Erro ao conectar com WhatsApp',
            ];
        }
    }
}
