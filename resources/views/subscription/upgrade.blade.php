@extends('layouts.app')

@section('title', 'Upgrade da Assinatura')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900">Upgrade da Assinatura</h1>
            <p class="mt-2 text-gray-600">Desbloqueie todas as funcionalidades do QR Code SaaS</p>
        </div>

        <!-- Status da Assinatura -->
        @if($user->hasActiveSubscription())
            <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">
                            Assinatura Ativa
                        </h3>
                        <div class="mt-2 text-sm text-green-700">
                            <p>Você já possui uma assinatura ativa. Aproveite todas as funcionalidades!</p>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($user->isOnTrial())
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            Período de Teste Ativo
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Seu período de teste expira em {{ $user->trial_ends_at->diffForHumans() }}. 
                                Faça upgrade agora para continuar usando todas as funcionalidades.</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Assinatura Expirada
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>Sua assinatura expirou. Faça upgrade para continuar usando todas as funcionalidades.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Plano Premium -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $planData['name'] }}</h2>
                    <div class="mt-4">
                        <span class="text-4xl font-bold text-primary-600">R$ {{ number_format($planData['price'], 2, ',', '.') }}</span>
                        <span class="text-gray-500">/mês</span>
                    </div>
                    <p class="mt-2 text-gray-600">Cancele a qualquer momento</p>
                </div>

                <!-- Recursos do Plano -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">O que está incluído:</h3>
                    <ul class="space-y-3">
                        @foreach($planData['features'] as $feature)
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Opções de Pagamento -->
                @if(!$user->hasActiveSubscription())
                    <div class="space-y-4">
                        <!-- Cartão de Crédito -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900">Cartão de Crédito</h4>
                                    <p class="text-sm text-gray-500">Assinatura recorrente mensal</p>
                                </div>
                                <button id="subscribe-credit-card" class="btn-primary">
                                    Assinar com Cartão
                                </button>
                            </div>
                        </div>

                        <!-- PIX -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900">PIX</h4>
                                    <p class="text-sm text-gray-500">Pagamento único mensal</p>
                                </div>
                                <button id="subscribe-pix" class="btn-primary">
                                    Pagar com PIX
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Gerenciar Assinatura -->
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Gerenciar Assinatura</h3>
                        <div class="space-x-4">
                            <button id="cancel-subscription" class="btn-outline text-red-600 border-red-300 hover:bg-red-50">
                                Cancelar Assinatura
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn-primary">
                                Voltar ao Dashboard
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Informações Adicionais -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="w-12 h-12 bg-primary-100 rounded-lg mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Seguro</h3>
                <p class="text-sm text-gray-500">Pagamentos processados com segurança pelo Mercado Pago</p>
            </div>

            <div class="text-center">
                <div class="w-12 h-12 bg-primary-100 rounded-lg mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Flexível</h3>
                <p class="text-sm text-gray-500">Cancele a qualquer momento sem taxas</p>
            </div>

            <div class="text-center">
                <div class="w-12 h-12 bg-primary-100 rounded-lg mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Suporte</h3>
                <p class="text-sm text-gray-500">Suporte prioritário para assinantes</p>
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

    // Função para mostrar modal
    function showModal(message, action) {
        messageEl.textContent = message;
        confirmBtn.onclick = action;
        modal.classList.remove('hidden');
    }

    // Função para esconder modal
    function hideModal() {
        modal.classList.add('hidden');
    }

    // Event listeners
    cancelBtn.addEventListener('click', hideModal);
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideModal();
        }
    });

    // Assinatura com cartão
    document.getElementById('subscribe-credit-card')?.addEventListener('click', function() {
        showModal('Você será redirecionado para o Mercado Pago para finalizar o pagamento com cartão de crédito.', function() {
            // Aqui você integraria com o Mercado Pago
            alert('Integração com Mercado Pago será implementada aqui');
            hideModal();
        });
    });

    // Pagamento com PIX
    document.getElementById('subscribe-pix')?.addEventListener('click', function() {
        showModal('Você será redirecionado para o Mercado Pago para gerar o PIX.', function() {
            // Aqui você integraria com o Mercado Pago
            alert('Integração com PIX será implementada aqui');
            hideModal();
        });
    });

    // Cancelar assinatura
    document.getElementById('cancel-subscription')?.addEventListener('click', function() {
        showModal('Tem certeza que deseja cancelar sua assinatura? Você perderá acesso às funcionalidades premium.', function() {
            // Aqui você faria a requisição para cancelar
            alert('Cancelamento será implementado aqui');
            hideModal();
        });
    });
});
</script>
@endsection
