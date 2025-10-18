@extends('layouts.app')

@section('title', 'Domínios Customizados')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Domínios Customizados</h1>
                <p class="mt-2 text-gray-600">Configure seu próprio domínio para URLs personalizadas</p>
            </div>
            <a href="{{ route('domains.create') }}" class="btn-primary">
                Adicionar Domínio
            </a>
        </div>

        <!-- Status da Assinatura -->
        @if(!auth()->user()->hasActiveSubscription())
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            Funcionalidade Premium
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Domínios customizados estão disponíveis apenas para assinantes premium. 
                                <a href="{{ route('subscription.upgrade') }}" class="font-medium underline">Faça upgrade agora</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Lista de Domínios -->
        @if($domains->count() > 0)
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-6">
                        @foreach($domains as $domain)
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <h3 class="text-lg font-medium text-gray-900">{{ $domain->domain }}</h3>
                                            
                                            <!-- Status Badge -->
                                            @if($domain->status === 'verified')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    Verificado
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                    </svg>
                                                    Pendente
                                                </span>
                                            @endif

                                            @if($domain->is_primary)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Primário
                                                </span>
                                            @endif
                                        </div>

                                        <div class="mt-2 text-sm text-gray-500">
                                            <p>Criado em {{ $domain->created_at->format('d/m/Y H:i') }}</p>
                                            @if($domain->verified_at)
                                                <p>Verificado em {{ $domain->verified_at->format('d/m/Y H:i') }}</p>
                                            @endif
                                        </div>

                                        @if($domain->status === 'pending')
                                            <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                                <p class="text-sm text-yellow-800">
                                                    <strong>Registro DNS necessário:</strong> {{ $domain->dns_record }}
                                                </p>
                                                <p class="text-xs text-yellow-600 mt-1">
                                                    Adicione este registro TXT no seu provedor de DNS para verificar o domínio.
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        @if($domain->status === 'pending')
                                            <button onclick="verifyDomain({{ $domain->id }})" class="btn-outline text-sm">
                                                Verificar
                                            </button>
                                        @endif

                                        @if($domain->status === 'verified' && !$domain->is_primary)
                                            <button onclick="setPrimary({{ $domain->id }})" class="btn-outline text-sm">
                                                Definir como Primário
                                            </button>
                                        @endif

                                        <a href="{{ route('domains.instructions', $domain) }}" class="btn-outline text-sm">
                                            Instruções
                                        </a>

                                        <button onclick="deleteDomain({{ $domain->id }})" class="btn-outline text-red-600 border-red-300 hover:bg-red-50 text-sm">
                                            Remover
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <!-- Estado Vazio -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum domínio customizado</h3>
                <p class="mt-1 text-sm text-gray-500">Comece adicionando seu primeiro domínio customizado.</p>
                <div class="mt-6">
                    <a href="{{ route('domains.create') }}" class="btn-primary">
                        Adicionar Domínio
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

    window.verifyDomain = function(domainId) {
        fetch(`/domains/${domainId}/verify`, {
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
            alert('Erro ao verificar domínio');
        });
    };

    window.setPrimary = function(domainId) {
        showModal('Definir este domínio como primário?', function() {
            fetch(`/domains/${domainId}/primary`, {
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
                alert('Erro ao definir domínio primário');
            });
            hideModal();
        });
    };

    window.deleteDomain = function(domainId) {
        showModal('Tem certeza que deseja remover este domínio? Esta ação não pode ser desfeita.', function() {
            fetch(`/domains/${domainId}`, {
                method: 'DELETE',
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
                alert('Erro ao remover domínio');
            });
            hideModal();
        });
    };
});
</script>
@endsection
