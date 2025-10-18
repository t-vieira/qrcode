@extends('layouts.app')

@section('title', 'Instruções de Configuração DNS')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <a href="{{ route('domains.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Instruções de Configuração DNS</h1>
                    <p class="mt-2 text-gray-600">Configure o registro DNS para verificar seu domínio</p>
                </div>
            </div>
        </div>

        <!-- Informações do Domínio -->
        <div class="bg-white shadow rounded-lg overflow-hidden mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Domínio: {{ $domain->domain }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Status</h3>
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
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Registro DNS</h3>
                        <div class="bg-gray-100 border border-gray-300 rounded p-3">
                            <code class="text-sm text-gray-800">{{ $domain->dns_record }}</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($domain->status === 'pending')
            <!-- Instruções para Configuração -->
            <div class="bg-white shadow rounded-lg overflow-hidden mb-8">
                <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Como Configurar</h2>
                    
                    <div class="space-y-6">
                        <!-- Passo 1 -->
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-primary-100 text-primary-600 text-sm font-medium">
                                    1
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-900">Acesse seu provedor de DNS</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Entre no painel de controle do seu provedor de DNS (ex: Cloudflare, GoDaddy, Namecheap, etc.)
                                </p>
                            </div>
                        </div>

                        <!-- Passo 2 -->
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-primary-100 text-primary-600 text-sm font-medium">
                                    2
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-900">Adicione o registro TXT</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Crie um novo registro TXT com o valor exato mostrado acima
                                </p>
                                <div class="mt-2 bg-gray-50 border border-gray-200 rounded p-3">
                                    <div class="text-xs text-gray-600 space-y-1">
                                        <div><strong>Tipo:</strong> TXT</div>
                                        <div><strong>Nome:</strong> @ (ou deixe em branco)</div>
                                        <div><strong>Valor:</strong> {{ $domain->dns_record }}</div>
                                        <div><strong>TTL:</strong> 300 (ou padrão)</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Passo 3 -->
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-primary-100 text-primary-600 text-sm font-medium">
                                    3
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-900">Aguarde a propagação</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    A propagação DNS pode levar de alguns minutos a 24 horas
                                </p>
                            </div>
                        </div>

                        <!-- Passo 4 -->
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-primary-100 text-primary-600 text-sm font-medium">
                                    4
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-900">Verifique o domínio</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Clique no botão abaixo para verificar se o registro foi configurado corretamente
                                </p>
                                <div class="mt-3">
                                    <button onclick="verifyDomain({{ $domain->id }})" class="btn-primary">
                                        Verificar Domínio
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Exemplos por Provedor -->
        <div class="bg-white shadow rounded-lg overflow-hidden mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Exemplos por Provedor</h2>
                
                <div class="space-y-6">
                    <!-- Cloudflare -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Cloudflare</h3>
                        <div class="bg-gray-50 border border-gray-200 rounded p-4">
                            <ol class="text-sm text-gray-600 space-y-1">
                                <li>1. Acesse o painel do Cloudflare</li>
                                <li>2. Selecione seu domínio</li>
                                <li>3. Vá em "DNS" → "Records"</li>
                                <li>4. Clique em "Add record"</li>
                                <li>5. Selecione "TXT" como tipo</li>
                                <li>6. Deixe "Name" em branco ou use "@"</li>
                                <li>7. Cole o valor: <code class="bg-white px-1 rounded">{{ $domain->dns_record }}</code></li>
                                <li>8. Clique em "Save"</li>
                            </ol>
                        </div>
                    </div>

                    <!-- GoDaddy -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 mb-2">GoDaddy</h3>
                        <div class="bg-gray-50 border border-gray-200 rounded p-4">
                            <ol class="text-sm text-gray-600 space-y-1">
                                <li>1. Acesse o painel do GoDaddy</li>
                                <li>2. Vá em "My Products" → "DNS"</li>
                                <li>3. Clique em "Manage" no seu domínio</li>
                                <li>4. Clique em "Add" para adicionar um registro</li>
                                <li>5. Selecione "TXT" como tipo</li>
                                <li>6. Deixe "Host" em branco ou use "@"</li>
                                <li>7. Cole o valor: <code class="bg-white px-1 rounded">{{ $domain->dns_record }}</code></li>
                                <li>8. Clique em "Save"</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Namecheap -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Namecheap</h3>
                        <div class="bg-gray-50 border border-gray-200 rounded p-4">
                            <ol class="text-sm text-gray-600 space-y-1">
                                <li>1. Acesse o painel do Namecheap</li>
                                <li>2. Vá em "Domain List" → "Manage"</li>
                                <li>3. Clique na aba "Advanced DNS"</li>
                                <li>4. Clique em "Add New Record"</li>
                                <li>5. Selecione "TXT Record"</li>
                                <li>6. Deixe "Host" em branco ou use "@"</li>
                                <li>7. Cole o valor: <code class="bg-white px-1 rounded">{{ $domain->dns_record }}</code></li>
                                <li>8. Clique em "Save All Changes"</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Troubleshooting -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Solução de Problemas</h2>
                
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">O domínio não está sendo verificado</h3>
                        <ul class="mt-1 text-sm text-gray-600 space-y-1">
                            <li>• Verifique se o registro TXT foi adicionado corretamente</li>
                            <li>• Aguarde até 24 horas para a propagação DNS</li>
                            <li>• Use ferramentas como <a href="https://dnschecker.org" target="_blank" class="text-primary-600 hover:text-primary-500">DNS Checker</a> para verificar a propagação</li>
                            <li>• Certifique-se de que o valor está exatamente como mostrado acima</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Ainda não funciona após 24 horas</h3>
                        <ul class="mt-1 text-sm text-gray-600 space-y-1">
                            <li>• Entre em contato com o suporte do seu provedor de DNS</li>
                            <li>• Verifique se não há outros registros TXT conflitantes</li>
                            <li>• Tente remover e readicionar o registro</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function verifyDomain(domainId) {
    const button = event.target;
    const originalText = button.textContent;
    
    button.disabled = true;
    button.textContent = 'Verificando...';
    
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
            location.reload();
        } else {
            alert(data.message || 'Domínio ainda não foi verificado. Verifique se o registro DNS foi configurado corretamente.');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao verificar domínio');
    })
    .finally(() => {
        button.disabled = false;
        button.textContent = originalText;
    });
}
</script>
@endsection
