@extends('layouts.admin')

@section('title', 'Gerenciar QR Codes')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Gerenciar QR Codes</h1>
                        <p class="mt-2 text-gray-600">Visualize e gerencie todos os QR Codes do sistema</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('admin.qr-codes') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                        <input type="text" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Nome do QR Code ou usuário..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                        <select id="type" 
                                name="type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Todos</option>
                            <option value="url" {{ request('type') === 'url' ? 'selected' : '' }}>URL</option>
                            <option value="text" {{ request('type') === 'text' ? 'selected' : '' }}>Texto</option>
                            <option value="email" {{ request('type') === 'email' ? 'selected' : '' }}>Email</option>
                            <option value="phone" {{ request('type') === 'phone' ? 'selected' : '' }}>Telefone</option>
                            <option value="wifi" {{ request('type') === 'wifi' ? 'selected' : '' }}>WiFi</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabela de QR Codes -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    QR Codes ({{ $qrCodes->total() }})
                </h3>
            </div>
            
            @if($qrCodes->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($qrCodes as $qrCode)
                        <li class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($qrCode->file_path && \Storage::disk('public')->exists($qrCode->file_path))
                                            <img src="{{ \Storage::disk('public')->url($qrCode->file_path) }}" alt="{{ $qrCode->name }}" class="h-10 w-10 rounded">
                                        @else
                                            <div class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center">
                                            <p class="text-sm font-medium text-gray-900">{{ $qrCode->name }}</p>
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ ucfirst($qrCode->type) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500">{{ $qrCode->user->name }} ({{ $qrCode->user->email }})</p>
                                        <p class="text-sm text-gray-500">
                                            Criado em {{ $qrCode->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900">{{ $qrCode->scans->count() }} scans</div>
                                        <div class="text-sm text-gray-500">
                                            @if($qrCode->scans->count() > 0)
                                                Último scan: {{ $qrCode->scans->sortByDesc('scanned_at')->first()->scanned_at->format('d/m/Y') }}
                                            @else
                                                Nenhum scan
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.qr-codes.show', $qrCode) }}" 
                                           class="text-primary-600 hover:text-primary-900 text-sm font-medium">
                                            Ver
                                        </a>
                                        <a href="{{ route('admin.users.show', $qrCode->user) }}" 
                                           class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                                            Usuário
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                
                <!-- Paginação -->
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                    {{ $qrCodes->links() }}
                </div>
            @else
                <div class="px-4 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum QR Code encontrado</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(request()->hasAny(['search', 'type']))
                            Tente ajustar os filtros de busca.
                        @else
                            Ainda não há QR Codes no sistema.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
