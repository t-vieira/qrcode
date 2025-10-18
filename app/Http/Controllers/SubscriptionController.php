<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    protected MercadoPagoService $mercadoPagoService;

    public function __construct(MercadoPagoService $mercadoPagoService)
    {
        $this->mercadoPagoService = $mercadoPagoService;
    }

    /**
     * Página de upgrade da assinatura
     */
    public function upgrade(Request $request)
    {
        $user = $request->user();
        $planData = $this->mercadoPagoService->getPlanData();
        
        return view('subscription.upgrade', compact('user', 'planData'));
    }

    /**
     * Criar assinatura (cartão de crédito)
     */
    public function subscribe(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se já tem assinatura ativa
        if ($user->hasActiveSubscription()) {
            return response()->json([
                'success' => false,
                'message' => 'Você já possui uma assinatura ativa.',
            ], 400);
        }

        $validated = $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        $result = $this->mercadoPagoService->createSubscription($user, $validated);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Assinatura criada com sucesso!',
                'data' => $result,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['error'] ?? 'Erro ao criar assinatura.',
        ], 400);
    }

    /**
     * Criar pagamento PIX
     */
    public function createPixPayment(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se já tem assinatura ativa
        if ($user->hasActiveSubscription()) {
            return response()->json([
                'success' => false,
                'message' => 'Você já possui uma assinatura ativa.',
            ], 400);
        }

        $result = $this->mercadoPagoService->createPixPayment($user);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Pagamento PIX criado com sucesso!',
                'data' => $result,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['error'] ?? 'Erro ao criar pagamento PIX.',
        ], 400);
    }

    /**
     * Cancelar assinatura
     */
    public function cancel(Request $request): JsonResponse
    {
        $user = $request->user();
        $subscription = $user->activeSubscription;

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma assinatura ativa encontrada.',
            ], 400);
        }

        $result = $this->mercadoPagoService->cancelSubscription($subscription);

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Assinatura cancelada com sucesso.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Erro ao cancelar assinatura.',
        ], 500);
    }

    /**
     * Webhook do Mercado Pago
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            
            Log::info('Webhook Mercado Pago recebido:', $data);

            // Verificar se é um webhook válido
            if (!$this->isValidWebhook($request, $data)) {
                Log::warning('Webhook inválido recebido');
                return response()->json(['error' => 'Webhook inválido'], 400);
            }

            $result = $this->mercadoPagoService->processWebhook($data);

            if ($result) {
                return response()->json(['status' => 'success']);
            }

            return response()->json(['error' => 'Erro ao processar webhook'], 500);

        } catch (\Exception $e) {
            Log::error('Erro no webhook Mercado Pago: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno'], 500);
        }
    }

    /**
     * Página de sucesso do pagamento
     */
    public function success(Request $request)
    {
        $status = $request->get('status');
        $paymentId = $request->get('payment_id');
        $preapprovalId = $request->get('preapproval_id');

        if ($status === 'approved') {
            $message = 'Pagamento aprovado com sucesso! Sua assinatura está ativa.';
            $type = 'success';
        } elseif ($status === 'pending') {
            $message = 'Pagamento pendente. Você receberá uma confirmação por email.';
            $type = 'warning';
        } else {
            $message = 'Erro no processamento do pagamento. Tente novamente.';
            $type = 'error';
        }

        return view('subscription.result', compact('message', 'type', 'status', 'paymentId', 'preapprovalId'));
    }

    /**
     * Página de falha do pagamento
     */
    public function failure(Request $request)
    {
        $message = 'Pagamento não foi processado. Tente novamente ou entre em contato conosco.';
        $type = 'error';
        $status = 'failed';

        return view('subscription.result', compact('message', 'type', 'status'));
    }

    /**
     * Página de pagamento pendente
     */
    public function pending(Request $request)
    {
        $message = 'Pagamento pendente. Você receberá uma confirmação por email quando for processado.';
        $type = 'warning';
        $status = 'pending';

        return view('subscription.result', compact('message', 'type', 'status'));
    }

    /**
     * Verificar se o webhook é válido
     */
    protected function isValidWebhook(Request $request, array $data): bool
    {
        // Verificar se tem os campos obrigatórios
        if (!isset($data['type']) || !isset($data['action'])) {
            return false;
        }

        // Verificar se tem dados
        if (!isset($data['data']) || !isset($data['data']['id'])) {
            return false;
        }

        // Verificar se o webhook vem do Mercado Pago
        $userAgent = $request->header('User-Agent');
        if (!$userAgent || !str_contains($userAgent, 'MercadoPago')) {
            return false;
        }

        return true;
    }

    /**
     * Obter status da assinatura
     */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        $subscription = $user->activeSubscription;

        if (!$subscription) {
            return response()->json([
                'has_subscription' => false,
                'status' => $user->subscription_status,
                'trial_ends_at' => $user->trial_ends_at,
            ]);
        }

        return response()->json([
            'has_subscription' => true,
            'subscription' => [
                'id' => $subscription->id,
                'status' => $subscription->status,
                'plan_name' => $subscription->plan_name,
                'amount' => $subscription->amount,
                'current_period_start' => $subscription->current_period_start,
                'current_period_end' => $subscription->current_period_end,
                'canceled_at' => $subscription->canceled_at,
            ],
            'user_status' => $user->subscription_status,
            'trial_ends_at' => $user->trial_ends_at,
        ]);
    }
}
