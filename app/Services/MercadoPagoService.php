<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use MercadoPago\SDK;
use MercadoPago\Preapproval;
use MercadoPago\Payment;
use MercadoPago\Preference;
use Illuminate\Support\Facades\Log;

class MercadoPagoService
{
    protected string $accessToken;
    protected string $publicKey;

    public function __construct()
    {
        $this->accessToken = config('mercadopago.access_token');
        $this->publicKey = config('mercadopago.public_key');
        
        SDK::setAccessToken($this->accessToken);
    }

    /**
     * Criar assinatura recorrente (cartão de crédito)
     */
    public function createSubscription(User $user, array $paymentData): array
    {
        try {
            $preapproval = new Preapproval();
            $preapproval->payer_email = $user->email;
            $preapproval->back_url = route('subscription.success');
            $preapproval->reason = 'Assinatura QR Code SaaS - Plano Premium';
            $preapproval->auto_recurring = [
                'frequency' => 1,
                'frequency_type' => 'months',
                'transaction_amount' => 29.90,
                'currency_id' => 'BRL',
                'start_date' => now()->addDay()->format('Y-m-d\TH:i:s.000-04:00'),
                'end_date' => now()->addYear()->format('Y-m-d\TH:i:s.000-04:00'),
            ];
            $preapproval->payment_method_id = $paymentData['payment_method_id'] ?? 'credit_card';
            $preapproval->external_reference = 'user_' . $user->id;

            $preapproval->save();

            if ($preapproval->id) {
                // Criar registro de assinatura no banco
                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'mp_preapproval_id' => $preapproval->id,
                    'status' => 'pending',
                    'plan_name' => 'premium',
                    'amount' => 29.90,
                    'current_period_start' => now(),
                    'current_period_end' => now()->addMonth(),
                ]);

                return [
                    'success' => true,
                    'subscription_id' => $subscription->id,
                    'mp_preapproval_id' => $preapproval->id,
                    'init_point' => $preapproval->init_point,
                ];
            }

            return [
                'success' => false,
                'error' => 'Erro ao criar assinatura no Mercado Pago',
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao criar assinatura Mercado Pago: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'Erro interno do servidor',
            ];
        }
    }

    /**
     * Gerar preferência de pagamento para PIX
     */
    public function createPixPayment(User $user): array
    {
        try {
            $preference = new Preference();
            $preference->items = [
                [
                    'title' => 'Assinatura QR Code SaaS - Plano Premium',
                    'quantity' => 1,
                    'unit_price' => 29.90,
                    'currency_id' => 'BRL',
                ]
            ];
            $preference->payer = [
                'email' => $user->email,
                'name' => $user->name,
            ];
            $preference->payment_methods = [
                'excluded_payment_types' => [
                    ['id' => 'credit_card'],
                    ['id' => 'debit_card'],
                ],
                'installments' => 1,
            ];
            $preference->back_urls = [
                'success' => route('subscription.success'),
                'failure' => route('subscription.failure'),
                'pending' => route('subscription.pending'),
            ];
            $preference->auto_return = 'approved';
            $preference->external_reference = 'user_' . $user->id . '_' . time();

            $preference->save();

            if ($preference->id) {
                return [
                    'success' => true,
                    'preference_id' => $preference->id,
                    'init_point' => $preference->init_point,
                ];
            }

            return [
                'success' => false,
                'error' => 'Erro ao gerar pagamento PIX',
            ];

        } catch (\Exception $e) {
            Log::error('Erro ao criar pagamento PIX: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'Erro interno do servidor',
            ];
        }
    }

    /**
     * Processar webhook do Mercado Pago
     */
    public function processWebhook(array $data): bool
    {
        try {
            $type = $data['type'] ?? null;
            $action = $data['action'] ?? null;
            $data = $data['data'] ?? null;

            if (!$type || !$action || !$data) {
                return false;
            }

            switch ($type) {
                case 'preapproval':
                    return $this->processPreapprovalWebhook($action, $data);
                
                case 'payment':
                    return $this->processPaymentWebhook($action, $data);
                
                default:
                    Log::info('Webhook tipo não processado: ' . $type);
                    return true;
            }

        } catch (\Exception $e) {
            Log::error('Erro ao processar webhook Mercado Pago: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Processar webhook de preapproval (assinatura)
     */
    protected function processPreapprovalWebhook(string $action, array $data): bool
    {
        $preapprovalId = $data['id'] ?? null;
        
        if (!$preapprovalId) {
            return false;
        }

        $subscription = Subscription::where('mp_preapproval_id', $preapprovalId)->first();
        
        if (!$subscription) {
            Log::warning('Assinatura não encontrada para preapproval_id: ' . $preapprovalId);
            return false;
        }

        switch ($action) {
            case 'authorized':
                $subscription->update([
                    'status' => 'authorized',
                    'current_period_start' => now(),
                    'current_period_end' => now()->addMonth(),
                ]);
                
                $subscription->user->update([
                    'subscription_status' => 'active',
                    'subscription_id' => $subscription->id,
                ]);
                
                Log::info('Assinatura autorizada: ' . $subscription->id);
                break;

            case 'paused':
                $subscription->update(['status' => 'paused']);
                $subscription->user->update(['subscription_status' => 'canceled']);
                Log::info('Assinatura pausada: ' . $subscription->id);
                break;

            case 'cancelled':
                $subscription->update([
                    'status' => 'cancelled',
                    'canceled_at' => now(),
                ]);
                $subscription->user->update(['subscription_status' => 'canceled']);
                Log::info('Assinatura cancelada: ' . $subscription->id);
                break;

            default:
                Log::info('Ação de preapproval não processada: ' . $action);
        }

        return true;
    }

    /**
     * Processar webhook de pagamento
     */
    protected function processPaymentWebhook(string $action, array $data): bool
    {
        $paymentId = $data['id'] ?? null;
        
        if (!$paymentId) {
            return false;
        }

        // Buscar pagamento no Mercado Pago
        $payment = Payment::find_by_id($paymentId);
        
        if (!$payment) {
            Log::warning('Pagamento não encontrado: ' . $paymentId);
            return false;
        }

        $externalReference = $payment->external_reference;
        
        if (!$externalReference || !str_starts_with($externalReference, 'user_')) {
            return false;
        }

        $userId = str_replace('user_', '', explode('_', $externalReference)[0]);
        $user = User::find($userId);
        
        if (!$user) {
            Log::warning('Usuário não encontrado para pagamento: ' . $paymentId);
            return false;
        }

        switch ($action) {
            case 'payment.created':
            case 'payment.updated':
                if ($payment->status === 'approved') {
                    // Ativar assinatura se for pagamento único
                    $user->update([
                        'subscription_status' => 'active',
                        'trial_ends_at' => null,
                    ]);
                    
                    Log::info('Pagamento aprovado para usuário: ' . $user->id);
                }
                break;

            case 'payment.cancelled':
            case 'payment.rejected':
                Log::info('Pagamento cancelado/rejeitado: ' . $paymentId);
                break;
        }

        return true;
    }

    /**
     * Cancelar assinatura
     */
    public function cancelSubscription(Subscription $subscription): bool
    {
        try {
            if ($subscription->mp_preapproval_id) {
                $preapproval = Preapproval::find_by_id($subscription->mp_preapproval_id);
                
                if ($preapproval) {
                    $preapproval->status = 'cancelled';
                    $preapproval->save();
                }
            }

            $subscription->update([
                'status' => 'cancelled',
                'canceled_at' => now(),
            ]);

            $subscription->user->update([
                'subscription_status' => 'canceled',
            ]);

            Log::info('Assinatura cancelada: ' . $subscription->id);
            
            return true;

        } catch (\Exception $e) {
            Log::error('Erro ao cancelar assinatura: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar status da assinatura
     */
    public function checkSubscriptionStatus(Subscription $subscription): bool
    {
        try {
            if (!$subscription->mp_preapproval_id) {
                return false;
            }

            $preapproval = Preapproval::find_by_id($subscription->mp_preapproval_id);
            
            if (!$preapproval) {
                return false;
            }

            $oldStatus = $subscription->status;
            $newStatus = $this->mapMercadoPagoStatus($preapproval->status);

            if ($oldStatus !== $newStatus) {
                $subscription->update(['status' => $newStatus]);
                
                // Atualizar status do usuário
                $userStatus = $newStatus === 'authorized' ? 'active' : 'canceled';
                $subscription->user->update(['subscription_status' => $userStatus]);
                
                Log::info("Status da assinatura atualizado: {$subscription->id} de {$oldStatus} para {$newStatus}");
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Erro ao verificar status da assinatura: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mapear status do Mercado Pago para nosso sistema
     */
    protected function mapMercadoPagoStatus(string $mpStatus): string
    {
        return match ($mpStatus) {
            'authorized' => 'authorized',
            'paused' => 'paused',
            'cancelled' => 'cancelled',
            default => 'pending',
        };
    }

    /**
     * Obter dados do plano
     */
    public function getPlanData(): array
    {
        return [
            'name' => 'Premium',
            'price' => 29.90,
            'currency' => 'BRL',
            'interval' => 'month',
            'features' => [
                'QR Codes ilimitados',
                'QR Codes dinâmicos',
                'Estatísticas avançadas',
                'URLs personalizadas',
                'Domínio próprio',
                'Suporte prioritário',
            ],
        ];
    }
}
