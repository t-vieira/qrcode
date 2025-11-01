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
                        <li class="px-4 py-4 sm:px-6 hover:bg-gray-50 transition-colors">
                            <!-- Cabeçalho do Scan (sempre visível) -->
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-start flex-1">
                                    <div class="flex-shrink-0 mr-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center shadow-sm">
                                            @if($scan->is_robot)
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                                </svg>
                                            @else
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center flex-wrap gap-2 mb-2">
                                            <h3 class="text-base font-semibold text-gray-900">
                                                {{ $scan->device_type ? ucfirst($scan->device_type) : 'Dispositivo' }}
                                                @if($scan->device_model)
                                                    <span class="text-gray-600">• {{ $scan->device_model }}</span>
                                                @endif
                                            </h3>
                                            @if($scan->is_unique)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Único
                                                </span>
                                            @endif
                                            @if($scan->is_robot)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    Bot
                                                </span>
                                            @endif
                                            @if($scan->is_proxy)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Proxy/VPN
                                                </span>
                                            @endif
                                            @if($scan->is_mobile_connection)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Conexão Móvel
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $scan->scanned_at->format('d/m/Y H:i:s') }}
                                            </span>
                                            @if($scan->full_location)
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    {{ $scan->full_location }}
                                                </span>
                                            @endif
                                            @if($scan->ip_address)
                                                <span class="flex items-center font-mono">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                                    </svg>
                                                    {{ $scan->ip_address }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <button 
                                    onclick="toggleScanDetails({{ $scan->id }})"
                                    class="ml-4 text-gray-400 hover:text-gray-600 transition-colors"
                                    aria-label="Expandir detalhes">
                                    <svg id="icon-{{ $scan->id }}" class="w-5 h-5 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Detalhes Expandidos (inicialmente ocultos) -->
                            <div id="details-{{ $scan->id }}" class="hidden mt-4 pt-4 border-t border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <!-- Dispositivo e Navegador -->
                                    <div>
                                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Dispositivo & Navegador</h4>
                                        <dl class="space-y-1.5 text-sm">
                                            @if($scan->browser_with_version)
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-600">Navegador:</dt>
                                                    <dd class="text-gray-900 font-medium">{{ $scan->browser_with_version }}</dd>
                                                </div>
                                            @endif
                                            @if($scan->os_with_version)
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-600">Sistema:</dt>
                                                    <dd class="text-gray-900 font-medium">{{ $scan->os_with_version }}</dd>
                                                </div>
                                            @endif
                                            @if($scan->device_model)
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-600">Modelo:</dt>
                                                    <dd class="text-gray-900 font-medium">{{ $scan->device_model }}</dd>
                                                </div>
                                            @endif
                                            @if($scan->language)
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-600">Idioma:</dt>
                                                    <dd class="text-gray-900 font-medium">{{ strtoupper($scan->language) }}</dd>
                                                </div>
                                            @endif
                                        </dl>
                                    </div>

                                    <!-- Localização -->
                                    <div>
                                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Localização</h4>
                                        <dl class="space-y-1.5 text-sm">
                                            @if($scan->city)
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-600">Cidade:</dt>
                                                    <dd class="text-gray-900 font-medium">{{ $scan->city }}</dd>
                                                </div>
                                            @endif
                                            @if($scan->region)
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-600">Região:</dt>
                                                    <dd class="text-gray-900 font-medium">{{ $scan->region }}</dd>
                                                </div>
                                            @endif
                                            @if($scan->country)
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-600">País:</dt>
                                                    <dd class="text-gray-900 font-medium">{{ $scan->country }}</dd>
                                                </div>
                                            @endif
                                            @if($scan->postal_code)
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-600">CEP:</dt>
                                                    <dd class="text-gray-900 font-medium">{{ $scan->postal_code }}</dd>
                                                </div>
                                            @endif
                                            @if($scan->timezone)
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-600">Fuso:</dt>
                                                    <dd class="text-gray-900 font-medium">{{ $scan->timezone }}</dd>
                                                </div>
                                            @endif
                                            @if($scan->coordinates)
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-600">Coordenadas:</dt>
                                                    <dd class="text-gray-900 font-mono text-xs">
                                                        <a href="https://www.google.com/maps?q={{ $scan->latitude }},{{ $scan->longitude }}" 
                                                           target="_blank" 
                                                           class="text-primary-600 hover:text-primary-800 underline">
                                                            {{ $scan->coordinates }}
                                                        </a>
                                                    </dd>
                                                </div>
                                            @endif
                                        </dl>
                                    </div>

                                    <!-- Rede & Conexão -->
                                    <div>
                                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Rede & Conexão</h4>
                                        <dl class="space-y-1.5 text-sm">
                                            @if($scan->isp)
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-600">ISP:</dt>
                                                    <dd class="text-gray-900 font-medium truncate ml-2" title="{{ $scan->isp }}">{{ $scan->isp }}</dd>
                                                </div>
                                            @endif
                                            @if($scan->organization)
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-600">Organização:</dt>
                                                    <dd class="text-gray-900 font-medium truncate ml-2" title="{{ $scan->organization }}">{{ $scan->organization }}</dd>
                                                </div>
                                            @endif
                                            @if($scan->as_number)
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-600">AS Number:</dt>
                                                    <dd class="text-gray-900 font-mono text-xs">{{ $scan->as_number }}</dd>
                                                </div>
                                            @endif
                                            @if($scan->protocol)
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-600">Protocolo:</dt>
                                                    <dd class="text-gray-900 font-medium uppercase">{{ $scan->protocol }}</dd>
                                                </div>
                                            @endif
                                            @if($scan->referer)
                                                <div class="flex justify-between">
                                                    <dt class="text-gray-600">Referer:</dt>
                                                    <dd class="text-gray-900 text-xs truncate ml-2" title="{{ $scan->referer }}">
                                                        <a href="{{ $scan->referer }}" target="_blank" class="text-primary-600 hover:text-primary-800 underline">
                                                            {{ Str::limit($scan->referer, 40) }}
                                                        </a>
                                                    </dd>
                                                </div>
                                            @endif
                                        </dl>
                                    </div>
                                </div>

                                <!-- User Agent (expandível) -->
                                @if($scan->user_agent)
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <button 
                                            onclick="toggleUserAgent({{ $scan->id }})"
                                            class="text-xs font-semibold text-gray-500 uppercase hover:text-gray-700 flex items-center">
                                            <span>User Agent</span>
                                            <svg id="ua-icon-{{ $scan->id }}" class="w-4 h-4 ml-1 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        <div id="ua-{{ $scan->id }}" class="hidden mt-2 p-3 bg-gray-50 rounded-md">
                                            <code class="text-xs text-gray-700 break-all">{{ $scan->user_agent }}</code>
                                        </div>
                                    </div>
                                @endif
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

@push('scripts')
<script>
    function toggleScanDetails(scanId) {
        const details = document.getElementById('details-' + scanId);
        const icon = document.getElementById('icon-' + scanId);
        
        if (details.classList.contains('hidden')) {
            details.classList.remove('hidden');
            icon.classList.add('rotate-180');
        } else {
            details.classList.add('hidden');
            icon.classList.remove('rotate-180');
        }
    }

    function toggleUserAgent(scanId) {
        const uaDiv = document.getElementById('ua-' + scanId);
        const uaIcon = document.getElementById('ua-icon-' + scanId);
        
        if (uaDiv.classList.contains('hidden')) {
            uaDiv.classList.remove('hidden');
            uaIcon.classList.add('rotate-180');
        } else {
            uaDiv.classList.add('hidden');
            uaIcon.classList.remove('rotate-180');
        }
    }
</script>
@endpush
@endsection
