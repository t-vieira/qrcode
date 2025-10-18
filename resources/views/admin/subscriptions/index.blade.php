@extends('layouts.admin')

@section('title', 'Gerenciar Assinaturas')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Gerenciar Assinaturas</h1>
                        <p class="mt-2 text-gray-600">Visualize e gerencie todas as assinaturas do sistema</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('admin.subscriptions') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="status" 
                                name="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Todos</option>
                            <option value="authorized" {{ request('status') === 'authorized' ? 'selected' : '' }}>Autorizada</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                        <input type="text" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Nome ou email do usuário..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabela de Assinaturas -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Assinaturas ({{ $subscriptions->total() }})
                </h3>
            </div>
            
            @if($subscriptions->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($subscriptions as $subscription)
                        <li class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-primary-500 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">
                                                {{ substr($subscription->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center">
                                            <p class="text-sm font-medium text-gray-900">{{ $subscription->user->name }}</p>
                                        </div>
                                        <p class="text-sm text-gray-500">{{ $subscription->user->email }}</p>
                                        <p class="text-sm text-gray-500">
                                            Criada em {{ $subscription->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($subscription->status === 'authorized') bg-green-100 text-green-800
                                            @elseif($subscription->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($subscription->status === 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($subscription->status) }}
                                        </span>
                                        <p class="text-sm text-gray-500 mt-1">
                                            @if($subscription->amount)
                                                R$ {{ number_format($subscription->amount / 100, 2, ',', '.') }}
                                            @else
                                                Valor não informado
                                            @endif
                                        </p>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.users.show', $subscription->user) }}" 
                                           class="text-primary-600 hover:text-primary-900 text-sm font-medium">
                                            Ver Usuário
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                
                <!-- Paginação -->
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                    {{ $subscriptions->links() }}
                </div>
            @else
                <div class="px-4 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma assinatura encontrada</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(request()->hasAny(['search', 'status']))
                            Tente ajustar os filtros de busca.
                        @else
                            Ainda não há assinaturas no sistema.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
