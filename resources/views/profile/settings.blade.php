@extends('layouts.app')

@section('title', 'Configurações da Conta')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <a href="{{ route('profile.show') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Configurações da Conta</h1>
                    <p class="mt-2 text-gray-600">Gerencie as configurações e preferências da sua conta</p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Configurações de Notificação -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Notificações</h2>
                <p class="text-sm text-gray-600 mb-4">
                    Configure como você deseja receber notificações sobre sua conta e QR codes.
                </p>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Email de marketing</h3>
                            <p class="text-sm text-gray-500">Receba dicas, novidades e ofertas especiais</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Notificações de atividade</h3>
                            <p class="text-sm text-gray-500">Receba alertas sobre scans dos seus QR codes</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Lembretes de assinatura</h3>
                            <p class="text-sm text-gray-500">Receba lembretes sobre renovação da sua assinatura</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Configurações de Privacidade -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Privacidade</h2>
                <p class="text-sm text-gray-600 mb-4">
                    Controle como suas informações são usadas e compartilhadas.
                </p>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Perfil público</h3>
                            <p class="text-sm text-gray-500">Permitir que outros usuários vejam seu perfil</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Analytics anônimos</h3>
                            <p class="text-sm text-gray-500">Ajude-nos a melhorar o serviço compartilhando dados de uso anônimos</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Configurações de Segurança -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Segurança</h2>
                <p class="text-sm text-gray-600 mb-4">
                    Gerencie a segurança da sua conta e dados.
                </p>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Autenticação de dois fatores</h3>
                            <p class="text-sm text-gray-500">Adicione uma camada extra de segurança à sua conta</p>
                        </div>
                        <button class="px-4 py-2 text-sm font-medium text-primary-600 bg-primary-50 border border-primary-200 rounded-md hover:bg-primary-100 transition-colors">
                            Configurar
                        </button>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Sessões ativas</h3>
                            <p class="text-sm text-gray-500">Visualize e gerencie suas sessões ativas</p>
                        </div>
                        <button class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded-md hover:bg-gray-100 transition-colors">
                            Ver sessões
                        </button>
                    </div>
                </div>
            </div>

            <!-- Zona de Perigo -->
            <div class="bg-white rounded-lg shadow-sm border border-red-200 p-6">
                <h2 class="text-lg font-medium text-red-900 mb-4">Zona de Perigo</h2>
                <p class="text-sm text-red-600 mb-4">
                    Ações irreversíveis que afetam permanentemente sua conta.
                </p>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-red-900">Exportar dados</h3>
                            <p class="text-sm text-red-600">Baixe uma cópia de todos os seus dados</p>
                        </div>
                        <button class="px-4 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 transition-colors">
                            Exportar
                        </button>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-red-900">Excluir conta</h3>
                            <p class="text-sm text-red-600">Exclua permanentemente sua conta e todos os dados associados</p>
                        </div>
                        <button onclick="confirmDeleteAccount()" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 transition-colors">
                            Excluir conta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação para Exclusão -->
<div id="deleteAccountModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Excluir conta</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita e todos os seus dados serão permanentemente removidos.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <form method="POST" action="{{ route('profile.destroy') }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Digite sua senha para confirmar:
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                               required>
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" 
                                onclick="closeDeleteAccountModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Excluir conta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDeleteAccount() {
    document.getElementById('deleteAccountModal').classList.remove('hidden');
}

function closeDeleteAccountModal() {
    document.getElementById('deleteAccountModal').classList.add('hidden');
}
</script>
@endsection
