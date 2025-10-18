<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        // Removido MercadoPagoService para evitar erro de dependência
    }

    /**
     * Página de upgrade da assinatura
     */
    public function upgrade(Request $request)
    {
        $user = $request->user();
        
        // Dados do plano simplificados
        $planData = [
            'name' => 'Plano Premium',
            'price' => 29.90,
            'currency' => 'BRL',
            'description' => 'Acesso completo a todas as funcionalidades',
            'features' => [
                'QR Codes dinâmicos ilimitados',
                'Estatísticas detalhadas',
                'Domínio personalizado',
                'Suporte prioritário',
                'Exportação de dados',
                'API completa'
            ]
        ];
        
        return view('subscription.upgrade', compact('user', 'planData'));
    }

    /**
     * Criar assinatura (cartão de crédito)
     */
    public function subscribe(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se já tem assinatura ativa
        if ($user->subscription_status === 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Você já possui uma assinatura ativa.',
            ], 400);
        }

        $validated = $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        // Simulação de criação de assinatura
        // Em produção, aqui seria feita a integração real com o Mercado Pago
        return response()->json([
            'success' => false,
            'message' => 'Integração com Mercado Pago em desenvolvimento. Use PIX temporariamente.',
        ], 400);
    }

    /**
     * Criar pagamento PIX
     */
    public function createPixPayment(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se já tem assinatura ativa
        if ($user->subscription_status === 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Você já possui uma assinatura ativa.',
            ], 400);
        }

        // Simulação de criação de pagamento PIX
        // Em produção, aqui seria feita a integração real com o Mercado Pago
        return response()->json([
            'success' => false,
            'message' => 'Integração com Mercado Pago em desenvolvimento. Funcionalidade temporariamente indisponível.',
        ], 400);
    }

    /**
     * Cancelar assinatura
     */
    public function cancel(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->subscription_status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma assinatura ativa encontrada.',
            ], 400);
        }

        // Simulação de cancelamento
        // Em produção, aqui seria feita a integração real com o Mercado Pago
        return response()->json([
            'success' => false,
            'message' => 'Integração com Mercado Pago em desenvolvimento. Entre em contato para cancelar.',
        ], 400);
    }

    /**
     * Webhook do Mercado Pago
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            
            Log::info('Webhook Mercado Pago recebido:', $data);

            // Simulação de processamento de webhook
            // Em produção, aqui seria feita a integração real com o Mercado Pago
            return response()->json(['status' => 'success', 'message' => 'Webhook recebido (modo desenvolvimento)']);

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
     * Obter status da assinatura
     */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'has_subscription' => $user->subscription_status === 'active',
            'status' => $user->subscription_status,
            'trial_ends_at' => $user->trial_ends_at,
            'message' => 'Status da assinatura (modo desenvolvimento)'
        ]);
    }
}
