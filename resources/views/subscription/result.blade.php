@extends('layouts.app')

@section('title', 'Resultado do Pagamento')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="text-center">
            <!-- Ícone baseado no tipo -->
            @if($type === 'success')
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            @elseif($type === 'warning')
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            @else
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            @endif

            <!-- Título -->
            <h1 class="text-2xl font-bold text-gray-900 mb-4">
                @if($type === 'success')
                    Pagamento Aprovado!
                @elseif($type === 'warning')
                    Pagamento Pendente
                @else
                    Erro no Pagamento
                @endif
            </h1>

            <!-- Mensagem -->
            <p class="text-lg text-gray-600 mb-8">{{ $message }}</p>

            <!-- Detalhes do Pagamento -->
            @if(isset($paymentId) || isset($preapprovalId))
                <div class="bg-gray-50 rounded-lg p-6 mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Detalhes da Transação</h3>
                    <div class="space-y-2 text-sm">
                        @if(isset($paymentId))
                            <div class="flex justify-between">
                                <span class="text-gray-500">ID do Pagamento:</span>
                                <span class="font-mono text-gray-900">{{ $paymentId }}</span>
                            </div>
                        @endif
                        @if(isset($preapprovalId))
                            <div class="flex justify-between">
                                <span class="text-gray-500">ID da Assinatura:</span>
                                <span class="font-mono text-gray-900">{{ $preapprovalId }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-500">Status:</span>
                            <span class="font-medium text-gray-900">{{ ucfirst($status) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Data:</span>
                            <span class="text-gray-900">{{ now()->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Próximos Passos -->
            @if($type === 'success')
                <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-8">
                    <h4 class="text-sm font-medium text-green-800 mb-2">Próximos Passos:</h4>
                    <ul class="text-sm text-green-700 space-y-1">
                        <li>• Sua assinatura está ativa e você já pode usar todas as funcionalidades</li>
                        <li>• Você receberá um email de confirmação em breve</li>
                        <li>• Acesse seu dashboard para começar a criar QR Codes</li>
                    </ul>
                </div>
            @elseif($type === 'warning')
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-8">
                    <h4 class="text-sm font-medium text-yellow-800 mb-2">O que acontece agora:</h4>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• Seu pagamento está sendo processado</li>
                        <li>• Você receberá um email quando for aprovado</li>
                        <li>• Enquanto isso, você pode continuar usando o período de teste</li>
                    </ul>
                </div>
            @else
                <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-8">
                    <h4 class="text-sm font-medium text-red-800 mb-2">O que fazer:</h4>
                    <ul class="text-sm text-red-700 space-y-1">
                        <li>• Verifique os dados do seu cartão ou conta</li>
                        <li>• Tente novamente com outro método de pagamento</li>
                        <li>• Entre em contato conosco se o problema persistir</li>
                    </ul>
                </div>
            @endif

            <!-- Botões de Ação -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @if($type === 'success')
                    <a href="{{ route('dashboard') }}" class="btn-primary">
                        Ir para Dashboard
                    </a>
                @elseif($type === 'warning')
                    <a href="{{ route('dashboard') }}" class="btn-primary">
                        Continuar no Dashboard
                    </a>
                @else
                    <a href="{{ route('subscription.upgrade') }}" class="btn-primary">
                        Tentar Novamente
                    </a>
                @endif
                
                <a href="{{ route('dashboard') }}" class="btn-outline">
                    Voltar ao Dashboard
                </a>
            </div>

            <!-- Informações de Suporte -->
            <div class="mt-12 pt-8 border-t border-gray-200">
                <p class="text-sm text-gray-500 mb-4">
                    Precisa de ajuda? Entre em contato conosco:
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center text-sm">
                    <a href="mailto:suporte@qrcodesaas.com" class="text-primary-600 hover:text-primary-500">
                        suporte@qrcodesaas.com
                    </a>
                    <span class="hidden sm:inline text-gray-300">•</span>
                    <a href="#" class="text-primary-600 hover:text-primary-500">
                        WhatsApp: (11) 99999-9999
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
