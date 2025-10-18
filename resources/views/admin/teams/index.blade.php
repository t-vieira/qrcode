@extends('layouts.admin')

@section('title', 'Gerenciar Equipes')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Gerenciar Equipes</h1>
                        <p class="mt-2 text-gray-600">Visualize e gerencie todas as equipes do sistema</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('admin.teams') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                        <input type="text" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Nome da equipe ou proprietário..."
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

        <!-- Tabela de Equipes -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Equipes ({{ $teams->total() }})
                </h3>
            </div>
            
            @if($teams->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($teams as $team)
                        <li class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">
                                                {{ substr($team->name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center">
                                            <p class="text-sm font-medium text-gray-900">{{ $team->name }}</p>
                                        </div>
                                        <p class="text-sm text-gray-500">Proprietário: {{ $team->owner->name }} ({{ $team->owner->email }})</p>
                                        <p class="text-sm text-gray-500">
                                            Criada em {{ $team->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900">{{ $team->users->count() }} membros</div>
                                        <div class="text-sm text-gray-500">
                                            @if($team->description)
                                                {{ Str::limit($team->description, 50) }}
                                            @else
                                                Sem descrição
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.users.show', $team->owner) }}" 
                                           class="text-primary-600 hover:text-primary-900 text-sm font-medium">
                                            Proprietário
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                
                <!-- Paginação -->
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                    {{ $teams->links() }}
                </div>
            @else
                <div class="px-4 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma equipe encontrada</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(request()->has('search'))
                            Tente ajustar os filtros de busca.
                        @else
                            Ainda não há equipes no sistema.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
