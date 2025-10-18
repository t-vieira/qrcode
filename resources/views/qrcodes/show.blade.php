@extends('layouts.app')

@section('title', $qrCode->name)

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $qrCode->name }}</h1>
                    <p class="mt-2 text-gray-600">{{ ucfirst($qrCode->type) }} • Criado em {{ $qrCode->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('qrcodes.edit', $qrCode) }}" class="btn-secondary">
                        Editar
                    </a>
                    <a href="{{ route('qrcodes.index') }}" class="btn-primary">
                        Voltar
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- QR Code Preview -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Visualização</h3>
                    <div class="text-center">
                        <div class="inline-block p-4 bg-white border-2 border-gray-200 rounded-lg">
                            @if($qrCode->file_path && \Storage::disk('public')->exists($qrCode->file_path))
                                <img src="{{ \Storage::disk('public')->url($qrCode->file_path) }}" 
                                     alt="{{ $qrCode->name }}" 
                                     class="w-48 h-48 rounded">
                            @else
                                <div class="w-48 h-48 bg-gray-100 rounded flex items-center justify-center">
                                    <svg class="h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">QR Code não encontrado</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Download Options -->
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Download</h4>
                        <div class="flex space-x-2">
                            <a href="{{ route('qrcodes.download', [$qrCode, 'png']) }}" 
                               class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                                PNG
                            </a>
                            <a href="{{ route('qrcodes.download', [$qrCode, 'jpg']) }}" 
                               class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                                JPG
                            </a>
                            <a href="{{ route('qrcodes.download', [$qrCode, 'svg']) }}" 
                               class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                                SVG
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- QR Code Details -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Detalhes</h3>
                    
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nome</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $qrCode->name }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tipo</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($qrCode->type) }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Código Curto</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $qrCode->short_code }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $qrCode->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($qrCode->status) }}
                                </span>
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tipo de QR</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $qrCode->is_dynamic ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $qrCode->is_dynamic ? 'Dinâmico' : 'Estático' }}
                                </span>
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Conteúdo</dt>
                            <dd class="mt-1 text-sm text-gray-900 break-all">{{ $qrCode->content }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Criado em</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $qrCode->created_at->format('d/m/Y H:i:s') }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Atualizado em</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $qrCode->updated_at->format('d/m/Y H:i:s') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="mt-8 bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Estatísticas</h3>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_scans']) }}</div>
                        <div class="text-sm text-gray-500">Total de Scans</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['unique_scans']) }}</div>
                        <div class="text-sm text-gray-500">Scans Únicos</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['today_scans']) }}</div>
                        <div class="text-sm text-gray-500">Scans Hoje</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['this_week_scans']) }}</div>
                        <div class="text-sm text-gray-500">Esta Semana</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['this_month_scans']) }}</div>
                        <div class="text-sm text-gray-500">Este Mês</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Device Statistics -->
        @if(!empty($stats['device_stats']))
        <div class="mt-8 bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Scans por Dispositivo</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($stats['device_stats'] as $device => $count)
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ number_format($count) }}</div>
                            <div class="text-sm text-gray-500">{{ ucfirst($device) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Scans -->
        @if($recentScans->count() > 0)
        <div class="mt-8 bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Scans Recentes</h3>
                <div class="space-y-3">
                    @foreach($recentScans as $scan)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $scan->device_type ? ucfirst($scan->device_type) : 'Dispositivo' }}
                                        @if($scan->is_unique)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                Único
                                            </span>
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $scan->scanned_at->format('d/m/Y H:i') }}
                                        @if($scan->country)
                                            • {{ $scan->country }}
                                        @endif
                                        @if($scan->city)
                                            • {{ $scan->city }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                @if($scan->browser)
                                    <p class="text-sm text-gray-500">{{ $scan->browser }}</p>
                                @endif
                                @if($scan->os)
                                    <p class="text-sm text-gray-500">{{ $scan->os }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($stats['total_scans'] > 10)
                    <div class="mt-4 text-center">
                        <a href="{{ route('qrcodes.scans', $qrCode) }}" class="text-sm text-primary-600 hover:text-primary-900">
                            Ver todos os {{ number_format($stats['total_scans']) }} scans →
                        </a>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection