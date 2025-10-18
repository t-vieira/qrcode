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
                            <div class="w-48 h-48 bg-gray-100 rounded flex items-center justify-center">
                                <svg class="h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">QR Code será gerado aqui</p>
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
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">0</div>
                        <div class="text-sm text-gray-500">Total de Scans</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">0</div>
                        <div class="text-sm text-gray-500">Scans Únicos</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">0</div>
                        <div class="text-sm text-gray-500">Scans Hoje</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection