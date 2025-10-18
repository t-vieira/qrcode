@extends('layouts.app')

@section('title', 'Novo Ticket de Suporte')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <a href="{{ route('support.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Novo Ticket de Suporte</h1>
                    <p class="mt-2 text-gray-600">Descreva seu problema e nossa equipe te ajudará</p>
                </div>
            </div>
        </div>

        <!-- Formulário -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <form id="support-form">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Assunto -->
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700">
                                Assunto *
                            </label>
                            <div class="mt-1">
                                <input type="text" 
                                       name="subject" 
                                       id="subject"
                                       class="form-input block w-full sm:text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="Ex: Problema com QR Code dinâmico"
                                       required>
                            </div>
                        </div>

                        <!-- Prioridade -->
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700">
                                Prioridade
                            </label>
                            <div class="mt-1">
                                <select name="priority" 
                                        id="priority"
                                        class="form-select block w-full sm:text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
                                    <option value="normal">Normal</option>
                                    <option value="high">Alta</option>
                                    <option value="urgent">Urgente</option>
                                    <option value="low">Baixa</option>
                                </select>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Use "Urgente" apenas para problemas críticos que impedem o uso do sistema.
                            </p>
                        </div>

                        <!-- Telefone (opcional) -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">
                                Telefone (opcional)
                            </label>
                            <div class="mt-1">
                                <input type="tel" 
                                       name="phone" 
                                       id="phone"
                                       class="form-input block w-full sm:text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="(11) 99999-9999">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Se informado, você pode receber notificações via WhatsApp.
                            </p>
                        </div>

                        <!-- Mensagem -->
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700">
                                Descrição do Problema *
                            </label>
                            <div class="mt-1">
                                <textarea name="message" 
                                          id="message"
                                          rows="6"
                                          class="form-textarea block w-full sm:text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                                          placeholder="Descreva detalhadamente o problema que você está enfrentando..."
                                          required></textarea>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Seja o mais específico possível. Inclua passos para reproduzir o problema, se aplicável.
                            </p>
                        </div>

                        <!-- Informações Adicionais -->
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                            <h4 class="text-sm font-medium text-blue-800 mb-2">Dicas para um atendimento mais rápido:</h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• Descreva o que você estava tentando fazer</li>
                                <li>• Inclua mensagens de erro, se houver</li>
                                <li>• Informe o navegador e dispositivo que está usando</li>
                                <li>• Anexe screenshots se necessário</li>
                            </ul>
                        </div>

                        <!-- Botões -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('support.index') }}" class="btn-outline">
                                Cancelar
                            </a>
                            <button type="submit" class="btn-primary" id="submit-btn">
                                Enviar Ticket
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
                    <div id="result-content"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('support-form');
    const submitBtn = document.getElementById('submit-btn');
    const resultContainer = document.getElementById('result-container');
    const resultContent = document.getElementById('result-content');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        
        // Desabilitar botão
        submitBtn.disabled = true;
        submitBtn.textContent = 'Enviando...';
        
        fetch('/support', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                subject: formData.get('subject'),
                message: formData.get('message'),
                priority: formData.get('priority'),
                phone: formData.get('phone'),
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resultContent.innerHTML = `
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Ticket Criado com Sucesso!</h3>
                        <p class="text-gray-600 mb-4">${data.message}</p>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Ticket ID: #${data.ticket.id}</p>
                            <p class="text-sm text-gray-500">Assunto: ${data.ticket.subject}</p>
                            <p class="text-sm text-gray-500">Prioridade: ${data.ticket.priority}</p>
                        </div>
                        <div class="mt-6 space-x-3">
                            <a href="/support" class="btn-primary">
                                Ver Todos os Tickets
                            </a>
                            <a href="/support/tickets/${data.ticket.id}" class="btn-outline">
                                Ver Detalhes
                            </a>
                        </div>
                    </div>
                `;
                resultContainer.classList.remove('hidden');
                form.reset();
            } else {
                alert(data.message || 'Erro ao criar ticket');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao criar ticket');
        })
        .finally(() => {
            // Reabilitar botão
            submitBtn.disabled = false;
            submitBtn.textContent = 'Enviar Ticket';
        });
    });

    // Máscara para telefone
    document.getElementById('phone').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 11) {
            value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        } else if (value.length >= 7) {
            value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
        } else if (value.length >= 3) {
            value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
        }
        e.target.value = value;
    });
});
</script>
@endsection
