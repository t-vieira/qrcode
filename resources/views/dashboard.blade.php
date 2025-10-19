@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="mt-2 text-gray-600">Visão geral dos seus QR Codes e estatísticas</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-primary-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total de QR Codes</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_qr_codes'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total de Scans</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_scans'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Scans Únicos</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['unique_scans'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Scans Hoje</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['today_scans'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Status -->
        @if(auth()->user()->subscription_status === 'trialing')
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            Período de teste ativo
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Seu período de teste expira em {{ auth()->user()->trial_ends_at->diffForHumans() }}. 
                                <a href="{{ route('subscription.upgrade') }}" class="font-medium underline text-yellow-800 hover:text-yellow-900">
                                    Faça upgrade agora
                                </a> para continuar usando todas as funcionalidades.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(auth()->user()->subscription_status === 'expired')
            <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Assinatura expirada
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>Sua assinatura expirou. 
                                <a href="{{ route('subscription.upgrade') }}" class="font-medium underline text-red-800 hover:text-red-900">
                                    Renove agora
                                </a> para continuar usando todas as funcionalidades.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent QR Codes -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">QR Codes Recentes</h3>
                    <div class="space-y-3">
                        @forelse($recentQrCodes as $qrCode)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if($qrCode->file_path && \Storage::disk('public')->exists($qrCode->file_path))
                                            <img src="{{ \Storage::disk('public')->url($qrCode->file_path) }}" alt="{{ $qrCode->name }}" class="h-10 w-10 rounded">
                                        @else
                                            <div class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center">
                                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $qrCode->name }}</p>
                                        <p class="text-sm text-gray-500">{{ ucfirst($qrCode->type) }} • {{ $qrCode->scans()->count() }} scans</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('qrcodes.show', $qrCode) }}" class="text-primary-600 hover:text-primary-900 text-sm font-medium">
                                        Ver
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum QR Code</h3>
                                <p class="mt-1 text-sm text-gray-500">Comece criando seu primeiro QR Code.</p>
                                <div class="mt-6">
                                    <a href="/qrcodes/create" class="btn-primary">
                                        Criar QR Code
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    @if($recentQrCodes->count() > 0)
                        <div class="mt-4">
                            <a href="{{ route('qrcodes.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-500">
                                Ver todos os QR Codes →
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Scans Chart -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Scans dos Últimos 30 Dias</h3>
                    <div class="h-64 flex items-center justify-center">
                        <canvas id="scansChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Ações Rápidas</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <a href="/qrcodes/create" class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-primary-500 rounded-lg border border-gray-200 hover:border-gray-300">
                            <div>
                                <span class="rounded-lg inline-flex p-3 bg-primary-50 text-primary-700 ring-4 ring-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </span>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-lg font-medium">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    Criar QR Code
                                </h3>
                                <p class="mt-2 text-sm text-gray-500">Crie um novo QR Code personalizado</p>
                            </div>
                        </a>

                        <a href="{{ route('qrcodes.index') }}" class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-primary-500 rounded-lg border border-gray-200 hover:border-gray-300">
                            <div>
                                <span class="rounded-lg inline-flex p-3 bg-green-50 text-green-700 ring-4 ring-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </span>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-lg font-medium">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    Gerenciar QR Codes
                                </h3>
                                <p class="mt-2 text-sm text-gray-500">Visualize e gerencie seus QR Codes</p>
                            </div>
                        </a>

                        @if(auth()->user()->subscription_status === 'active')
                            <a href="{{ route('reports') }}" class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-primary-500 rounded-lg border border-gray-200 hover:border-gray-300">
                                <div>
                                    <span class="rounded-lg inline-flex p-3 bg-blue-50 text-blue-700 ring-4 ring-white">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <h3 class="text-lg font-medium">
                                        <span class="absolute inset-0" aria-hidden="true"></span>
                                        Relatórios
                                    </h3>
                                    <p class="mt-2 text-sm text-gray-500">Visualize estatísticas detalhadas</p>
                                </div>
                            </a>
                        @endif

                        <a href="{{ route('subscription.upgrade') }}" class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-primary-500 rounded-lg border border-gray-200 hover:border-gray-300">
                            <div>
                                <span class="rounded-lg inline-flex p-3 bg-yellow-50 text-yellow-700 ring-4 ring-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </span>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-lg font-medium">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    Upgrade
                                </h3>
                                <p class="mt-2 text-sm text-gray-500">Faça upgrade da sua conta</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de scans
    const ctx = document.getElementById('scansChart').getContext('2d');
    const scansData = @json($scansChartData ?? []);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: scansData.labels || [],
            datasets: [{
                label: 'Scans',
                data: scansData.data || [],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endsection
