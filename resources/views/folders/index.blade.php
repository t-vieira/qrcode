@extends('layouts.app')

@section('title', 'Pastas')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Pastas</h1>
                    <p class="text-gray-600 mt-1">Organize seus QR codes em pastas</p>
                </div>
                <a href="{{ route('folders.create') }}" 
                   class="btn-primary inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nova Pasta
                </a>
            </div>
        </div>

        <!-- Folders Grid -->
        @if($folders->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($folders as $folder)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $folder->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $folder->qr_codes_count }} QR {{ $folder->qr_codes_count == 1 ? 'Code' : 'Codes' }}</p>
                            </div>
                        </div>
                        
                        <!-- Folder Actions -->
                        <div class="flex items-center space-x-1">
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        class="p-1 text-gray-400 hover:text-gray-600 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                    </svg>
                                </button>
                                
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-transition
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                                    <div class="py-1">
                                        <a href="{{ route('folders.show', $folder) }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Ver Pasta
                                        </a>
                                        <a href="{{ route('folders.edit', $folder) }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Editar
                                        </a>
                                        <form action="{{ route('folders.destroy', $folder) }}" 
                                              method="POST" 
                                              class="block"
                                              onsubmit="return confirm('Tem certeza que deseja excluir esta pasta?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Excluir
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Subfolders -->
                    @if($folder->children->count() > 0)
                    <div class="mb-4">
                        <p class="text-xs text-gray-500 mb-2">{{ $folder->children->count() }} Subpasta{{ $folder->children->count() == 1 ? '' : 's' }}</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach($folder->children->take(3) as $child)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">
                                {{ $child->name }}
                            </span>
                            @endforeach
                            @if($folder->children->count() > 3)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">
                                +{{ $folder->children->count() - 3 }} mais
                            </span>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    <!-- Created Date -->
                    <div class="text-xs text-gray-500">
                        Criado em {{ $folder->created_at->format('d/m/Y') }}
                    </div>
                </div>
                
                <!-- View Folder Button -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                    <a href="{{ route('folders.show', $folder) }}" 
                       class="block w-full text-center py-2 px-4 bg-teal-600 text-white text-sm font-medium rounded-md hover:bg-teal-700 transition-colors duration-200">
                        Ver Pasta
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma pasta encontrada</h3>
            <p class="text-gray-500 mb-6">Crie sua primeira pasta para organizar seus QR codes.</p>
            <a href="{{ route('folders.create') }}" 
               class="btn-primary inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Criar Primeira Pasta
            </a>
        </div>
        @endif
    </div>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
