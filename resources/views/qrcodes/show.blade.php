@extends('layouts.app')

@section('title', $qrCode->name)

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $qrCode->name }}</h1>
                <p class="mt-2 text-gray-600">{{ ucfirst($qrCode->type) }} • Criado em {{ $qrCode->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('qrcodes.edit', $qrCode) }}" class="btn-outline">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar
                </a>
                <a href="{{ route('qrcodes.download', $qrCode) }}" class="btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Baixar
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- QR Code Preview -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">QR Code</h3>
                    <div class="flex justify-center">
                        @if($qrCode->file_path)
                            <img src="{{ Storage::url($qrCode->file_path) }}" alt="{{ $qrCode->name }}" class="max-w-full h-auto">
                        @else
                            <div class="w-64 h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                                <p class="text-gray-500">QR Code não disponível</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Informações do QR Code -->
                    <div class="mt-6 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">URL:</span>
                            <span class="text-sm text-gray-900">{{ $qrCode->url }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Código Curto:</span>
                            <span class="text-sm text-gray-900">{{ $qrCode->short_code }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Tipo:</span>
                            <span class="text-sm text-gray-900">{{ ucfirst($qrCode->type) }}</span>
                        </div>
                        @if($qrCode->is_dynamic)
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Tipo:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Dinâmico
                                </span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Status:</span>
                            @if($qrCode->status === 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Ativo
                                </span>
                            @elseif($qrCode->status === 'archived')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Arquivado
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Botões de Download -->
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Baixar em diferentes formatos:</h4>
                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('qrcodes.download', [$qrCode, 'png']) }}" class="btn-outline text-sm">
                                PNG
                            </a>
                            <a href="{{ route('qrcodes.download', [$qrCode, 'jpg']) }}" class="btn-outline text-sm">
                                JPG
                            </a>
                            <a href="{{ route('qrcodes.download', [$qrCode, 'svg']) }}" class="btn-outline text-sm">
                                SVG
                            </a>
                            <a href="{{ route('qrcodes.download', [$qrCode, 'eps']) }}" class="btn-outline text-sm">
                                EPS
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Estatísticas</h3>
                    
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $qrCode->total_scans }}</div>
                            <div class="text-sm text-gray-500">Total de Scans</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $qrCode->unique_scans }}</div>
                            <div class="text-sm text-gray-500">Scans Únicos</div>
                        </div>
                    </div>

                    @if($qrCode->last_scan)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Último Scan:</h4>
                            <div class="text-sm text-gray-500">
                                {{ $qrCode->last_scan->scanned_at->format('d/m/Y H:i') }}
                                @if($qrCode->last_scan->location)
                                    <br>{{ $qrCode->last_scan->location }}
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Scans Recentes -->
                    @if($qrCode->scans->count() > 0)
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Scans Recentes:</h4>
                            <div class="space-y-2">
                                @foreach($qrCode->scans as $scan)
                                    <div class="flex items-center justify-between text-sm">
                                        <div>
                                            <span class="text-gray-900">{{ $scan->scanned_at->format('d/m H:i') }}</span>
                                            @if($scan->location)
                                                <span class="text-gray-500">• {{ $scan->location }}</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            @if($scan->device_type)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ ucfirst($scan->device_type) }}
                                                </span>
                                            @endif
                                            @if($scan->is_unique)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    Único
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-sm text-gray-500">Nenhum scan registrado ainda</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Conteúdo do QR Code -->
        <div class="mt-8 bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Conteúdo</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($qrCode->content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
        </div>

        <!-- Ações -->
        <div class="mt-8 flex justify-between">
            <a href="{{ route('qrcodes.index') }}" class="btn-outline">
                ← Voltar para Lista
            </a>
            
            <div class="flex space-x-3">
                <form action="{{ route('qrcodes.destroy', $qrCode) }}" method="POST" 
                      onsubmit="return confirm('Tem certeza que deseja excluir este QR Code?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-outline text-red-600 border-red-300 hover:bg-red-50">
                        Excluir
                    </button>
                </form>
                
                <a href="{{ route('qrcodes.edit', $qrCode) }}" class="btn-primary">
                    Editar QR Code
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
