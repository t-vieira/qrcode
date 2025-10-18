@extends('layouts.app')

@section('title', 'Ticket #' . $ticket->id)

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="{{ route('support.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Ticket #{{ $ticket->id }}</h1>
                        <p class="mt-2 text-gray-600">{{ $ticket->subject }}</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    @if($ticket->status === 'open')
                        <button onclick="closeTicket({{ $ticket->id }})" class="btn-outline">
                            Fechar Ticket
                        </button>
                    @elseif($ticket->status === 'closed')
                        <button onclick="reopenTicket({{ $ticket->id }})" class="btn-primary">
                            Reabrir Ticket
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Informações do Ticket -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Status</h3>
                        @if($ticket->status === 'open')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Aberto
                            </span>
                        @elseif($ticket->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                Pendente
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                </svg>
                                Fechado
                            </span>
                        @endif
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Prioridade</h3>
                        @if($ticket->priority === 'urgent')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Urgente
                            </span>
                        @elseif($ticket->priority === 'high')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                Alta
                            </span>
                        @elseif($ticket->priority === 'normal')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Normal
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Baixa
                            </span>
                        @endif
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Criado em</h3>
                        <p class="text-sm text-gray-900">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensagem Original -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Mensagem Original</h3>
                <div class="prose max-w-none">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $ticket->message }}</p>
                </div>
            </div>
        </div>

        <!-- Resposta (se ticket estiver aberto) -->
        @if($ticket->status === 'open')
            <div class="bg-white shadow rounded-lg mb-6">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Adicionar Resposta</h3>
                    <form id="reply-form">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="reply-message" class="block text-sm font-medium text-gray-700">
                                    Sua mensagem
                                </label>
                                <div class="mt-1">
                                    <textarea name="message" 
                                              id="reply-message"
                                              rows="4"
                                              class="form-textarea block w-full sm:text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                                              placeholder="Adicione informações adicionais ou responda a perguntas da equipe de suporte..."
                                              required></textarea>
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" class="btn-primary" id="reply-btn">
                                    Enviar Resposta
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Histórico de Atividades -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Histórico de Atividades</h3>
                <div class="space-y-4">
                    <!-- Criação do ticket -->
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900">
                                <span class="font-medium">Ticket criado</span> por você
                            </p>
                            <p class="text-xs text-gray-500">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    @if($ticket->last_reply_at)
                        <!-- Última resposta -->
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">
                                    <span class="font-medium">Última atividade</span>
                                </p>
                                <p class="text-xs text-gray-500">{{ $ticket->last_reply_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($ticket->status === 'closed' && $ticket->closed_at)
                        <!-- Fechamento do ticket -->
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">
                                    <span class="font-medium">Ticket fechado</span>
                                </p>
                                <p class="text-xs text-gray-500">{{ $ticket->closed_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação -->
<div id="confirmation-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1000;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmar Ação</h3>
            <p id="confirmation-message" class="text-sm text-gray-500 mb-6"></p>
            <div class="flex justify-center space-x-4">
                <button id="confirm-action" class="btn-primary">
                    Confirmar
                </button>
                <button id="cancel-action" class="btn-outline">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('confirmation-modal');
    const confirmBtn = document.getElementById('confirm-action');
    const cancelBtn = document.getElementById('cancel-action');
    const messageEl = document.getElementById('confirmation-message');

    function showModal(message, action) {
        messageEl.textContent = message;
        confirmBtn.onclick = action;
        modal.classList.remove('hidden');
    }

    function hideModal() {
        modal.classList.add('hidden');
    }

    cancelBtn.addEventListener('click', hideModal);
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideModal();
        }
    });

    // Fechar ticket
    window.closeTicket = function(ticketId) {
        showModal('Tem certeza que deseja fechar este ticket?', function() {
            fetch(`/support/tickets/${ticketId}/close`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao fechar ticket');
            });
            hideModal();
        });
    };

    // Reabrir ticket
    window.reopenTicket = function(ticketId) {
        showModal('Tem certeza que deseja reabrir este ticket?', function() {
            fetch(`/support/tickets/${ticketId}/reopen`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao reabrir ticket');
            });
            hideModal();
        });
    };

    // Formulário de resposta
    const replyForm = document.getElementById('reply-form');
    if (replyForm) {
        replyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(replyForm);
            const replyBtn = document.getElementById('reply-btn');
            
            replyBtn.disabled = true;
            replyBtn.textContent = 'Enviando...';
            
            fetch(`/support/tickets/{{ $ticket->id }}/reply`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    message: formData.get('message'),
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao enviar resposta');
            })
            .finally(() => {
                replyBtn.disabled = false;
                replyBtn.textContent = 'Enviar Resposta';
            });
        });
    }
});
</script>
@endsection
