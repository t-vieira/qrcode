@extends('layouts.app')

@section('title', 'Adicionar Domínio Customizado')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <a href="{{ route('domains.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Adicionar Domínio Customizado</h1>
                    <p class="mt-2 text-gray-600">Configure seu próprio domínio para URLs personalizadas</p>
                </div>
            </div>
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

        <!-- Formulário -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <form id="domain-form">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Campo Domínio -->
                        <div>
                            <label for="domain" class="block text-sm font-medium text-gray-700">
                                Domínio
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="text" 
                                       name="domain" 
                                       id="domain"
                                       class="form-input block w-full pr-10 sm:text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="exemplo.com"
                                       required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9" />
                                    </svg>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Digite o domínio que você deseja usar (ex: meusite.com, qr.meusite.com)
                            </p>
                        </div>

                        <!-- Informações sobre Domínios -->
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                            <h4 class="text-sm font-medium text-blue-800 mb-2">Como funciona:</h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• Você adiciona seu domínio (ex: qr.meusite.com)</li>
                                <li>• Configuramos um registro DNS para verificação</li>
                                <li>• Após verificação, suas URLs ficam: qr.meusite.com/abc123</li>
                                <li>• Você pode ter até 5 domínios customizados</li>
                            </ul>
                        </div>

                        <!-- Exemplos -->
                        <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                            <h4 class="text-sm font-medium text-gray-800 mb-2">Exemplos de domínios válidos:</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• meusite.com</li>
                                <li>• qr.meusite.com</li>
                                <li>• qrcode.empresa.com.br</li>
                                <li>• link.minhaempresa.com</li>
                            </ul>
                        </div>

                        <!-- Botões -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('domains.index') }}" class="btn-outline">
                                Cancelar
                            </a>
                            <button type="submit" class="btn-primary" id="submit-btn">
                                Adicionar Domínio
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Resultado -->
        <div id="result-container" class="hidden mt-6">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Próximos Passos</h3>
                    <div id="result-content"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('domain-form');
    const submitBtn = document.getElementById('submit-btn');
    const resultContainer = document.getElementById('result-container');
    const resultContent = document.getElementById('result-content');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const domain = formData.get('domain');
        
        // Desabilitar botão
        submitBtn.disabled = true;
        submitBtn.textContent = 'Adicionando...';
        
        fetch('/domains', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ domain: domain }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resultContent.innerHTML = `
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-green-800 font-medium">Domínio adicionado com sucesso!</span>
                        </div>
                        
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <h4 class="text-sm font-medium text-yellow-800 mb-2">Configure o registro DNS:</h4>
                            <div class="bg-white border border-yellow-300 rounded p-3">
                                <code class="text-sm text-gray-800">${data.dns_record}</code>
                            </div>
                            <p class="text-xs text-yellow-600 mt-2">
                                Adicione este registro TXT no seu provedor de DNS (ex: Cloudflare, GoDaddy, etc.)
                            </p>
                        </div>
                        
                        <div class="flex space-x-3">
                            <button onclick="verifyDomain(${data.domain.id})" class="btn-primary">
                                Verificar Agora
                            </button>
                            <a href="/domains" class="btn-outline">
                                Ver Todos os Domínios
                            </a>
                        </div>
                    </div>
                `;
                resultContainer.classList.remove('hidden');
                form.reset();
            } else {
                alert(data.message || 'Erro ao adicionar domínio');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao adicionar domínio');
        })
        .finally(() => {
            // Reabilitar botão
            submitBtn.disabled = false;
            submitBtn.textContent = 'Adicionar Domínio';
        });
    });

    // Função para verificar domínio
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
                alert('Domínio verificado com sucesso!');
                window.location.href = '/domains';
            } else {
                alert(data.message || 'Erro ao verificar domínio');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao verificar domínio');
        });
    };
});
</script>
@endsection
