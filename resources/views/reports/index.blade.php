@extends('layouts.app')

@section('title', 'Estatísticas')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Estatísticas</h1>
                    <p class="mt-2 text-gray-600">Visão completa dos seus QR Codes mais escaneados e últimos scans</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-500">
                        Última atualização: {{ now()->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Scanned QR Codes -->
        @if(isset($top_qr_codes) && $top_qr_codes->count() > 0)
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">🔥 QR Codes Mais Escaneados</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach($top_qr_codes->take(5) as $index => $qrcode)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200 p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-teal-600 bg-teal-50 px-2 py-1 rounded">#{{ $index + 1 }}</span>
                        <span class="text-xs font-medium text-gray-500">{{ ucfirst($qrcode->type) }}</span>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 truncate mb-2" title="{{ $qrcode->name }}">{{ $qrcode->name }}</h3>
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-lg font-bold text-blue-600">{{ $qrcode->stats_total_scans ?? $qrcode->scans_count ?? 0 }}</div>
                            <div class="text-xs text-gray-500">Scans</div>
                        </div>
                        <a href="{{ route('qrcodes.show', $qrcode) }}" class="text-xs text-teal-600 hover:text-teal-700 font-medium">
                            Ver →
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recent Scans -->
        @if(isset($recent_scans) && $recent_scans->count() > 0)
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">📊 Últimos Scans</h2>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="divide-y divide-gray-200">
                    @foreach($recent_scans->take(10) as $scan)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2 mb-1">
                                    <a href="{{ route('qrcodes.show', $scan->qrCode) }}" class="text-sm font-semibold text-gray-900 hover:text-teal-600 truncate">
                                        {{ $scan->qrCode->name }}
                                    </a>
                                    <span class="text-xs text-gray-500">•</span>
                                    <span class="text-xs text-gray-500">{{ ucfirst($scan->qrCode->type) }}</span>
                                </div>
                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                    @if($scan->country)
                                        <span class="flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $scan->country }}
                                            @if($scan->city)
                                                , {{ $scan->city }}
                                            @endif
                                        </span>
                                    @endif
                                    @if($scan->device_type)
                                        <span class="flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            {{ ucfirst($scan->device_type) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-500">
                                    {{ \App\Helpers\DateHelper::formatWithMonth($scan->scanned_at) }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    {{ $scan->scanned_at->format('H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Stats Overview -->
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
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_qr_codes'] }}</dd>
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
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_scans'] }}</dd>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Esta Semana</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['this_week_scans'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Este Mês</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['this_month_scans'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Scans por Dia -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Scans por Dia (Últimos 30 dias)</h3>
                <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                    <div class="text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="text-gray-500">Gráfico de linha será implementado</p>
                        <p class="text-sm text-gray-400">Total de scans: {{ $stats['total_scans'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Scans por Tipo -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Scans por Tipo de QR Code</h3>
                <div class="space-y-4">
                    @forelse($scans_by_type as $type)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-primary-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-900 capitalize">{{ $type->type }}</span>
                            </div>
                            <span class="text-sm text-gray-500">{{ $type->total_scans }} scans</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">Nenhum scan registrado</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top QR Codes -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">QR Codes Mais Escaneados</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($top_qr_codes as $index => $qrCode)
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-primary-100 rounded-md flex items-center justify-center">
                                    <span class="text-sm font-medium text-primary-600">#{{ $index + 1 }}</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $qrCode->name }}</div>
                                <div class="text-sm text-gray-500 capitalize">{{ $qrCode->type }}</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900">{{ $qrCode->scans_count }} scans</div>
                                <div class="text-sm text-gray-500">Criado em {{ $qrCode->created_at->format('d/m/Y') }}</div>
                            </div>
                            <a href="{{ route('qrcodes.show', $qrCode) }}" class="text-primary-600 hover:text-primary-900 text-sm font-medium">
                                Ver detalhes
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500">Nenhum QR Code encontrado</p>
                        <p class="text-sm text-gray-400 mt-1">Crie seu primeiro QR Code para ver as estatísticas</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Additional Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Scans por Dispositivo -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Scans por Dispositivo</h3>
                <div class="space-y-4">
                    @foreach($scans_by_device as $device)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-900">{{ $device['device'] }}</span>
                            </div>
                            <span class="text-sm text-gray-500">{{ $device['scans'] }} scans</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Scans por País -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Scans por País</h3>
                <div class="space-y-4">
                    @foreach($scans_by_country as $country)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-900">{{ $country['country'] }}</span>
                            </div>
                            <span class="text-sm text-gray-500">{{ $country['scans'] }} scans</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Export Options -->
        <div class="mt-8 bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Exportar Relatórios</h3>
                    <p class="text-sm text-gray-500 mt-1">Baixe seus dados em diferentes formatos</p>
                </div>
                <div class="flex space-x-3">
                    <button class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        PDF
                    </button>
                    <button class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Excel
                    </button>
                    <button class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        CSV
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
