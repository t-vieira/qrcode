@extends('layouts.app')

@section('title', 'Suporte')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Suporte</h1>
                <p class="mt-2 text-gray-600">Central de ajuda e tickets de suporte</p>
            </div>
            <a href="{{ route('support.create') }}" class="btn-primary">
                Novo Ticket
            </a>
        </div>

        <!-- Status do WhatsApp -->
        <div id="whatsapp-status" class="mb-8">
            <!-- Será preenchido via JavaScript -->
        </div>

        <!-- Filtros -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex flex-wrap gap-4">
                    <select id="status-filter" class="form-select">
                        <option value="">Todos os status</option>
                        <option value="open">Abertos</option>
                        <option value="pending">Pendentes</option>
                        <option value="closed">Fechados</option>
                    </select>
                    
                    <select id="priority-filter" class="form-select">
                        <option value="">Todas as prioridades</option>
                        <option value="low">Baixa</option>
                        <option value="normal">Normal</option>
                        <option value="high">Alta</option>
                        <option value="urgent">Urgente</option>
                    </select>
                    
                    <button onclick="applyFilters()" class="btn-outline">
                        Filtrar
                    </button>
                </div>
            </div>
        </div>

        <!-- Lista de Tickets -->
        @if($tickets->count() > 0)
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-4">
                        @foreach($tickets as $ticket)
                            <div class="border border-gray-200 rounded-lg p-4 ticket-item" 
                                 data-status="{{ $ticket->status }}" 
                                 data-priority="{{ $ticket->priority }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h3 class="text-lg font-medium text-gray-900">
                                                #{{ $ticket->id }} - {{ $ticket->subject }}
                                            </h3>
                                            
                                            <!-- Status Badge -->
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

                                            <!-- Priority Badge -->
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

                                        <p class="text-sm text-gray-600 mb-2">
                                            {{ Str::limit($ticket->message, 150) }}
                                        </p>

                                        <div class="text-xs text-gray-500">
                                            <span>Criado em {{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                                            @if($ticket->last_reply_at)
                                                <span class="mx-2">•</span>
                                                <span>Última resposta em {{ $ticket->last_reply_at->format('d/m/Y H:i') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('support.show', $ticket) }}" class="btn-outline text-sm">
                                            Ver Detalhes
                                        </a>
                                        
                                        @if($ticket->status === 'open')
                                            <button onclick="closeTicket({{ $ticket->id }})" class="btn-outline text-sm">
                                                Fechar
                                            </button>
                                        @elseif($ticket->status === 'closed')
                                            <button onclick="reopenTicket({{ $ticket->id }})" class="btn-outline text-sm">
                                                Reabrir
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Paginação -->
                    <div class="mt-6">
                        {{ $tickets->links() }}
                    </div>
                </div>
            </div>
        @else
            <!-- Estado Vazio -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum ticket de suporte</h3>
                <p class="mt-1 text-sm text-gray-500">Você ainda não criou nenhum ticket de suporte.</p>
                <div class="mt-6">
                    <a href="{{ route('support.create') }}" class="btn-primary">
                        Criar Primeiro Ticket
                    </a>
                </div>
            </div>
        @endif
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

    // Carregar status do WhatsApp
    loadWhatsAppStatus();

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

    // Aplicar filtros
    window.applyFilters = function() {
        const statusFilter = document.getElementById('status-filter').value;
        const priorityFilter = document.getElementById('priority-filter').value;
        const tickets = document.querySelectorAll('.ticket-item');

        tickets.forEach(ticket => {
            const status = ticket.dataset.status;
            const priority = ticket.dataset.priority;

            let show = true;

            if (statusFilter && status !== statusFilter) {
                show = false;
            }

            if (priorityFilter && priority !== priorityFilter) {
                show = false;
            }

            ticket.style.display = show ? 'block' : 'none';
        });
    };

    // Carregar status do WhatsApp
    function loadWhatsAppStatus() {
        fetch('/support/status')
            .then(response => response.json())
            .then(data => {
                const statusContainer = document.getElementById('whatsapp-status');
                
                if (data.whatsapp_configured && data.support_enabled) {
                    statusContainer.innerHTML = `
                        <div class="bg-green-50 border border-green-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-green-800">
                                        Suporte WhatsApp Ativo
                                    </h3>
                                    <div class="mt-2 text-sm text-green-700">
                                        <p>Nosso suporte via WhatsApp está funcionando. Você pode receber notificações e respostas diretamente no seu WhatsApp.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    statusContainer.innerHTML = `
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        Suporte WhatsApp Indisponível
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>O suporte via WhatsApp está temporariamente indisponível. Você ainda pode criar tickets que serão respondidos por email.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Erro ao carregar status do WhatsApp:', error);
            });
    }
});
</script>
@endsection
