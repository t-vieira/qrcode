<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SupportController extends Controller
{
    protected WhatsAppService $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Mostrar página de suporte
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $tickets = $user->supportTickets()->latest()->paginate(10);
        
        return view('support.index', compact('tickets'));
    }

    /**
     * Mostrar formulário de contato
     */
    public function create()
    {
        return view('support.create');
    }

    /**
     * Criar novo ticket de suporte
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'priority' => 'nullable|string|in:low,normal,high,urgent',
            'phone' => 'nullable|string|max:20',
        ]);

        // Verificar se o usuário pode criar mais tickets
        $maxTickets = $user->hasActiveSubscription() ? 10 : 3;
        $openTickets = $user->supportTickets()->where('status', 'open')->count();
        
        if ($openTickets >= $maxTickets) {
            return response()->json([
                'success' => false,
                'message' => "Você pode ter no máximo {$maxTickets} ticket(s) aberto(s) simultaneamente.",
            ], 400);
        }

        try {
            // Criar ticket
            $ticket = SupportTicket::create([
                'user_id' => $user->id,
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'priority' => $validated['priority'] ?? 'normal',
                'status' => 'open',
            ]);

            // Enviar via WhatsApp se configurado
            if ($this->whatsAppService->isConfigured() && config('whatsapp.support.enabled')) {
                $result = $this->whatsAppService->sendSupportMessage(
                    $user,
                    $validated['message'],
                    $validated['priority'] ?? 'normal'
                );

                if ($result['success']) {
                    $ticket->update([
                        'whatsapp_message_id' => $result['message_id'] ?? null,
                    ]);
                }
            }

            Log::info("Ticket de suporte criado", [
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'subject' => $validated['subject'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ticket de suporte criado com sucesso! Nossa equipe responderá em breve.',
                'ticket' => $ticket,
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao criar ticket de suporte: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'data' => $validated,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar ticket de suporte. Tente novamente.',
            ], 500);
        }
    }

    /**
     * Mostrar detalhes do ticket
     */
    public function show(Request $request, SupportTicket $ticket)
    {
        $user = $request->user();
        
        // Verificar se o ticket pertence ao usuário
        if ($ticket->user_id !== $user->id) {
            abort(403, 'Você não tem acesso a este ticket.');
        }

        return view('support.show', compact('ticket'));
    }

    /**
     * Fechar ticket
     */
    public function close(Request $request, SupportTicket $ticket): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se o ticket pertence ao usuário
        if ($ticket->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem acesso a este ticket.',
            ], 403);
        }

        // Verificar se o ticket pode ser fechado
        if ($ticket->status === 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Este ticket já está fechado.',
            ], 400);
        }

        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        Log::info("Ticket de suporte fechado pelo usuário", [
            'user_id' => $user->id,
            'ticket_id' => $ticket->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket fechado com sucesso.',
        ]);
    }

    /**
     * Reabrir ticket
     */
    public function reopen(Request $request, SupportTicket $ticket): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se o ticket pertence ao usuário
        if ($ticket->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem acesso a este ticket.',
            ], 403);
        }

        // Verificar se o ticket pode ser reaberto
        if ($ticket->status !== 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Este ticket não está fechado.',
            ], 400);
        }

        $ticket->update([
            'status' => 'open',
            'closed_at' => null,
        ]);

        Log::info("Ticket de suporte reaberto pelo usuário", [
            'user_id' => $user->id,
            'ticket_id' => $ticket->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket reaberto com sucesso.',
        ]);
    }

    /**
     * Adicionar resposta ao ticket
     */
    public function reply(Request $request, SupportTicket $ticket): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se o ticket pertence ao usuário
        if ($ticket->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem acesso a este ticket.',
            ], 403);
        }

        // Verificar se o ticket está aberto
        if ($ticket->status === 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível responder a um ticket fechado.',
            ], 400);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        try {
            // Atualizar ticket com nova mensagem
            $ticket->update([
                'message' => $ticket->message . "\n\n--- Nova mensagem ---\n" . $validated['message'],
                'last_reply_at' => now(),
            ]);

            // Enviar via WhatsApp se configurado
            if ($this->whatsAppService->isConfigured() && config('whatsapp.support.enabled')) {
                $this->whatsAppService->sendSupportMessage(
                    $user,
                    "Resposta ao ticket #{$ticket->id}:\n\n" . $validated['message'],
                    $ticket->priority
                );
            }

            Log::info("Resposta adicionada ao ticket", [
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Resposta enviada com sucesso.',
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao adicionar resposta ao ticket: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar resposta. Tente novamente.',
            ], 500);
        }
    }

    /**
     * Webhook do WhatsApp
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            
            Log::info('Webhook WhatsApp recebido:', $data);

            // Verificar se é uma verificação de webhook
            if ($request->has('hub_mode') && $request->get('hub_mode') === 'subscribe') {
                $challenge = $this->whatsAppService->verifyWebhook(
                    $request->get('hub_verify_token'),
                    $request->get('hub_challenge')
                );

                if ($challenge) {
                    return response($challenge);
                }

                return response()->json(['error' => 'Token inválido'], 403);
            }

            // Processar webhook
            $result = $this->whatsAppService->processWebhook($data);

            if ($result) {
                return response()->json(['status' => 'success']);
            }

            return response()->json(['error' => 'Erro ao processar webhook'], 500);

        } catch (\Exception $e) {
            Log::error('Erro no webhook WhatsApp: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno'], 500);
        }
    }

    /**
     * Obter status do suporte
     */
    public function status(): JsonResponse
    {
        $isConfigured = $this->whatsAppService->isConfigured();
        $isEnabled = config('whatsapp.support.enabled');
        
        return response()->json([
            'whatsapp_configured' => $isConfigured,
            'support_enabled' => $isEnabled,
            'business_hours' => config('whatsapp.support.business_hours'),
            'auto_response' => config('whatsapp.support.auto_response'),
        ]);
    }

    /**
     * Enviar mensagem de teste
     */
    public function testMessage(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$this->whatsAppService->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'WhatsApp não está configurado.',
            ], 400);
        }

        $result = $this->whatsAppService->sendMessage(
            $user->phone ?? '',
            'Teste de mensagem do sistema de suporte QR Code SaaS. Se você recebeu esta mensagem, o WhatsApp está funcionando corretamente!'
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Mensagem de teste enviada com sucesso!',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['error'] ?? 'Erro ao enviar mensagem de teste.',
        ], 500);
    }
}
