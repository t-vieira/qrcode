@extends('layouts.admin')

@section('title', 'Detalhes do QR Code')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Detalhes do QR Code</h1>
                        <p class="mt-2 text-gray-600">Informações completas do QR Code</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.qr-codes') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informações do QR Code -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informações do QR Code</h3>
                    </div>
                    <div class="px-6 py-4 space-y-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-16 w-16">
                                @if($qrCode->file_path && \Storage::disk('public')->exists($qrCode->file_path))
                                    <img src="{{ \Storage::disk('public')->url($qrCode->file_path) }}" alt="{{ $qrCode->name }}" class="h-16 w-16 rounded">
                                @else
                                    <div class="h-16 w-16 rounded bg-gray-200 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <h4 class="text-xl font-medium text-gray-900">{{ $qrCode->name }}</h4>
                                <p class="text-gray-500">{{ ucfirst($qrCode->type) }}</p>
                                <div class="flex items-center mt-2 space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($qrCode->type) }}
                                    </span>
                                    <span class="text-sm text-gray-500">{{ $qrCode->scans->count() }} scans</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Criado em</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $qrCode->created_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Última atualização</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $qrCode->updated_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Proprietário</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <a href="{{ route('admin.users.show', $qrCode->user) }}" class="text-primary-600 hover:text-primary-900">
                                        {{ $qrCode->user->name }} ({{ $qrCode->user->email }})
                                    </a>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($qrCode->status === 'active') bg-green-100 text-green-800
                                        @elseif($qrCode->status === 'archived') bg-gray-100 text-gray-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($qrCode->status) }}
                                    </span>
                                </dd>
                            </div>
                        </div>

                        @if($qrCode->content)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Conteúdo</dt>
                                <dd class="mt-1 text-sm text-gray-900 break-all">{{ $qrCode->content }}</dd>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Scans do QR Code -->
                <div class="mt-8 bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Scans ({{ $qrCode->scans->count() }})</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse($qrCode->scans->sortByDesc('scanned_at')->take(10) as $scan)
                            <div class="px-6 py-4 flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">{{ $scan->ip_address }}</p>
                                        <p class="text-sm text-gray-500">{{ $scan->user_agent }}</p>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $scan->scanned_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-4 text-center text-gray-500">
                                Nenhum scan encontrado
                            </div>
                        @endforelse
                    </div>
                    @if($qrCode->scans->count() > 10)
                        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                            <a href="{{ route('qrcodes.scans', $qrCode) }}" class="text-sm text-primary-600 hover:text-primary-900">
                                Ver todos os scans →
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Estatísticas -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Estatísticas</h3>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Total de Scans</span>
                            <span class="text-sm font-medium text-gray-900">{{ $qrCode->scans->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Scans Únicos</span>
                            <span class="text-sm font-medium text-gray-900">{{ $qrCode->scans->where('is_unique', true)->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Scans Hoje</span>
                            <span class="text-sm font-medium text-gray-900">{{ $qrCode->scans->where('scanned_at', '>=', today())->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Ações</h3>
                    </div>
                    <div class="px-6 py-4 space-y-3">
                        <a href="{{ route('qrcodes.show', $qrCode) }}" target="_blank" class="w-full flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Ver QR Code
                        </a>
                        
                        <a href="{{ route('admin.users.show', $qrCode->user) }}" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            Ver Usuário
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
