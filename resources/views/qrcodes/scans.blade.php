@extends('layouts.app')

@section('title', 'Scans do QR Code - ' . $qrCode->name)

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <a href="{{ route('qrcodes.show', $qrCode) }}" class="mr-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Scans do QR Code</h1>
                    <p class="mt-2 text-gray-600">{{ $qrCode->name }} • {{ number_format($scans->total()) }} scans encontrados</p>
                </div>
            </div>
        </div>

        <!-- Estatísticas Resumidas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total de Scans</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($scans->total()) }}</dd>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Scans Únicos</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($scans->where('is_unique', true)->count()) }}</dd>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Hoje</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($scans->where('scanned_at', '>=', today())->count()) }}</dd>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Esta Semana</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($scans->where('scanned_at', '>=', now()->startOfWeek())->count()) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('qrcodes.scans', $qrCode) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="device_type" class="block text-sm font-medium text-gray-700 mb-2">Dispositivo</label>
                    <select id="device_type" 
                            name="device_type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Todos</option>
                        @foreach($deviceTypes as $device => $count)
                            <option value="{{ $device }}" {{ request('device_type') === $device ? 'selected' : '' }}>
                                {{ ucfirst($device) }} ({{ $count }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-2">País</label>
                    <select id="country" 
                            name="country"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Todos</option>
                        @foreach($countries as $country => $count)
                            <option value="{{ $country }}" {{ request('country') === $country ? 'selected' : '' }}>
                                {{ $country }} ({{ $count }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filtros</label>
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="unique_only" 
                               name="unique_only" 
                               value="1"
                               {{ request('unique_only') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                        <label for="unique_only" class="ml-2 text-sm text-gray-700">Apenas scans únicos</label>
                    </div>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Estatísticas por Dispositivo e País -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Dispositivos -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Scans por Dispositivo</h3>
                    <div class="space-y-3">
                        @foreach($deviceTypes as $device => $count)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                    <span class="text-sm font-medium text-gray-900">{{ ucfirst($device) }}</span>
                                </div>
                                <span class="text-sm text-gray-500">{{ number_format($count) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Países -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Scans por País</h3>
                    <div class="space-y-3">
                        @foreach($countries as $country => $count)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                    <span class="text-sm font-medium text-gray-900">{{ $country }}</span>
                                </div>
                                <span class="text-sm text-gray-500">{{ number_format($count) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Scans -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            @if($scans->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($scans as $scan)
                        <li class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-primary-500 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $scan->device_type ? ucfirst($scan->device_type) : 'Dispositivo' }}
                                                @if($scan->is_unique)
                                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Único
                                                    </span>
                                                @endif
                                            </p>
                                        </div>
                                        <p class="text-sm text-gray-500">
                                            {{ $scan->scanned_at->format('d/m/Y H:i:s') }}
                                            @if($scan->country)
                                                • {{ $scan->country }}
                                            @endif
                                            @if($scan->city)
                                                • {{ $scan->city }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <div class="text-right">
                                        @if($scan->browser)
                                            <p class="text-sm text-gray-900">{{ $scan->browser }}</p>
                                        @endif
                                        @if($scan->os)
                                            <p class="text-sm text-gray-500">{{ $scan->os }}</p>
                                        @endif
                                        @if($scan->ip_address)
                                            <p class="text-sm text-gray-500">{{ $scan->ip_address }}</p>
                                        @endif
                                    </div>
                                    
                                    <div class="text-right">
                                        <p class="text-sm text-gray-500">
                                            {{ $scan->scanned_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                
                <!-- Paginação -->
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                    {{ $scans->links() }}
                </div>
            @else
                <div class="px-4 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum scan encontrado</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(request()->hasAny(['device_type', 'country', 'unique_only']))
                            Tente ajustar os filtros de busca.
                        @else
                            Este QR Code ainda não foi escaneado.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
