@extends('layouts.app')

@section('title', 'Privacidade de Dados')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Privacidade de Dados</h1>
            <p class="mt-2 text-gray-600">Gerencie seus dados pessoais conforme a LGPD</p>
        </div>

        <!-- Status da Conta -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Status da Sua Conta</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-700">Conta Criada</h3>
                        <p class="text-lg text-gray-900">{{ $user->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-700">Última Atividade</h3>
                        <p class="text-lg text-gray-900">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-700">Status da Assinatura</h3>
                        <p class="text-lg text-gray-900">{{ ucfirst($user->subscription_status) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ações de Privacidade -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Exportar Dados -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Exportar Dados</h3>
                        <p class="text-gray-600">Baixe todos os seus dados</p>
                    </div>
                </div>
                <p class="text-gray-700 mb-4">
                    Você pode baixar todos os seus dados pessoais, incluindo QR Codes, estatísticas e configurações.
                </p>
                <button onclick="exportData()" class="btn-primary">
                    Exportar Meus Dados
                </button>
            </div>

            <!-- Solicitar Exclusão -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Solicitar Exclusão</h3>
                        <p class="text-gray-600">Excluir conta e dados permanentemente</p>
                    </div>
                </div>
                <p class="text-gray-700 mb-4">
                    <strong>Atenção:</strong> Esta ação é irreversível. Todos os seus dados serão excluídos permanentemente.
                </p>
                <button onclick="requestDeletion()" class="btn-outline text-red-600 border-red-300 hover:bg-red-50">
                    Solicitar Exclusão
                </button>
            </div>
        </div>

        <!-- Informações sobre Dados -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Seus Dados</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">QR Codes Criados</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ $user->qrCodes()->count() }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Total de Scans</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ $user->qrCodes()->sum('scans_count') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Tickets de Suporte</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ $user->supportTickets()->count() }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Equipes Participando</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ $user->teams()->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Política de Retenção -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-blue-900 mb-4">Política de Retenção de Dados</h3>
            <div class="text-blue-800 space-y-2">
                <p><strong>Dados Pessoais:</strong> Mantidos enquanto sua conta estiver ativa</p>
                <p><strong>QR Codes:</strong> QR Codes estáticos são mantidos indefinidamente</p>
                <p><strong>Estatísticas:</strong> Dados de scans são anonimizados após 1 ano</p>
                <p><strong>Logs de Sistema:</strong> Mantidos por 6 meses para segurança</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Exportação -->
<div id="export-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1000;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Exportar Dados</h3>
            <p class="text-sm text-gray-500 mb-6">
                Seus dados serão preparados e você receberá um link para download em alguns minutos.
            </p>
            <div class="flex justify-center space-x-4">
                <button onclick="confirmExport()" class="btn-primary">
                    Confirmar Exportação
                </button>
                <button onclick="closeExportModal()" class="btn-outline">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Exclusão -->
<div id="deletion-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1000;">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Solicitar Exclusão de Dados</h3>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-red-800">Atenção</h4>
                        <div class="mt-2 text-sm text-red-700">
                            <p>Esta ação é <strong>irreversível</strong>. Todos os seus dados serão excluídos permanentemente, incluindo:</p>
                            <ul class="mt-2 list-disc list-inside">
                                <li>Todos os seus QR Codes</li>
                                <li>Estatísticas e relatórios</li>
                                <li>Configurações da conta</li>
                                <li>Histórico de pagamentos</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <form id="deletion-form">
                @csrf
                <div class="mb-4">
                    <label for="deletion-reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Motivo da Exclusão (opcional)
                    </label>
                    <textarea name="reason" 
                              id="deletion-reason"
                              rows="3"
                              class="form-textarea block w-full sm:text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Conte-nos por que está solicitando a exclusão..."></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="confirm_deletion" 
                               class="form-checkbox h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                               required>
                        <span class="ml-2 text-sm text-gray-700">
                            Confirmo que desejo excluir permanentemente todos os meus dados
                        </span>
                    </label>
                </div>
                
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeDeletionModal()" class="btn-outline">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary bg-red-600 hover:bg-red-700">
                        Solicitar Exclusão
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function exportData() {
    document.getElementById('export-modal').classList.remove('hidden');
}

function closeExportModal() {
    document.getElementById('export-modal').classList.add('hidden');
}

function confirmExport() {
    fetch('/privacy/export', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Dados exportados com sucesso! Você receberá um email com o link para download.');
            closeExportModal();
        } else {
            alert('Erro ao exportar dados: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao exportar dados');
    });
}

function requestDeletion() {
    document.getElementById('deletion-modal').classList.remove('hidden');
}

function closeDeletionModal() {
    document.getElementById('deletion-modal').classList.add('hidden');
}

document.getElementById('deletion-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/privacy/request-deletion', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            reason: formData.get('reason'),
            confirm_deletion: formData.get('confirm_deletion') ? true : false,
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Solicitação de exclusão enviada. Você receberá um email de confirmação em breve.');
            closeDeletionModal();
        } else {
            alert('Erro ao solicitar exclusão: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao solicitar exclusão');
    });
});
</script>
@endsection
