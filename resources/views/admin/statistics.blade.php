@extends('layouts.admin')

@section('title', 'Estatísticas do Sistema')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Estatísticas do Sistema</h1>
                        <p class="mt-2 text-gray-600">Relatórios detalhados e métricas do sistema</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Estatísticas Principais -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Usuários -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total de Usuários</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['users']['total']) }}</p>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-3 gap-4">
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Ativos</p>
                        <p class="text-sm font-medium text-green-600">{{ number_format($stats['users']['active']) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Em Teste</p>
                        <p class="text-sm font-medium text-yellow-600">{{ number_format($stats['users']['trial']) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Inativos</p>
                        <p class="text-sm font-medium text-red-600">{{ number_format($stats['users']['inactive']) }}</p>
                    </div>
                </div>
            </div>

            <!-- QR Codes -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total de QR Codes</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['qr_codes']['total']) }}</p>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Este Mês</p>
                        <p class="text-sm font-medium text-blue-600">{{ number_format($stats['qr_codes']['this_month']) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Este Ano</p>
                        <p class="text-sm font-medium text-purple-600">{{ number_format($stats['qr_codes']['this_year']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Assinaturas -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total de Assinaturas</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['subscriptions']['total']) }}</p>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-3 gap-4">
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Ativas</p>
                        <p class="text-sm font-medium text-green-600">{{ number_format($stats['subscriptions']['active']) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Pendentes</p>
                        <p class="text-sm font-medium text-yellow-600">{{ number_format($stats['subscriptions']['pending']) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Canceladas</p>
                        <p class="text-sm font-medium text-red-600">{{ number_format($stats['subscriptions']['cancelled']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Equipes -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-indigo-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total de Equipes</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['teams']['total']) }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Este Mês</p>
                        <p class="text-sm font-medium text-indigo-600">{{ number_format($stats['teams']['this_month']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos de Crescimento -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Crescimento de Usuários</h3>
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900">
                        @if($growth_data['users_growth'] > 0)
                            +{{ $growth_data['users_growth'] }}%
                        @elseif($growth_data['users_growth'] < 0)
                            {{ $growth_data['users_growth'] }}%
                        @else
                            0%
                        @endif
                    </div>
                    <p class="text-sm text-gray-500">vs mês anterior</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Crescimento de QR Codes</h3>
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900">
                        @if($growth_data['qr_codes_growth'] > 0)
                            +{{ $growth_data['qr_codes_growth'] }}%
                        @elseif($growth_data['qr_codes_growth'] < 0)
                            {{ $growth_data['qr_codes_growth'] }}%
                        @else
                            0%
                        @endif
                    </div>
                    <p class="text-sm text-gray-500">vs mês anterior</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Crescimento de Assinaturas</h3>
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900">
                        @if($growth_data['subscriptions_growth'] > 0)
                            +{{ $growth_data['subscriptions_growth'] }}%
                        @elseif($growth_data['subscriptions_growth'] < 0)
                            {{ $growth_data['subscriptions_growth'] }}%
                        @else
                            0%
                        @endif
                    </div>
                    <p class="text-sm text-gray-500">vs mês anterior</p>
                </div>
            </div>
        </div>

        <!-- Resumo do Sistema -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Resumo do Sistema</h3>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['users']['total']) }}</div>
                        <div class="text-sm text-gray-500">Usuários Registrados</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ number_format($stats['qr_codes']['total']) }}</div>
                        <div class="text-sm text-gray-500">QR Codes Criados</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ number_format($stats['subscriptions']['total']) }}</div>
                        <div class="text-sm text-gray-500">Assinaturas</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-indigo-600">{{ number_format($stats['teams']['total']) }}</div>
                        <div class="text-sm text-gray-500">Equipes</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
