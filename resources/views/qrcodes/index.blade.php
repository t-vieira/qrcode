@extends('layouts.app')

@section('title', 'Meus Códigos QR')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Meus Códigos QR</h1>
        <p class="mt-2 text-gray-600">Gerencie todos os seus QR Codes</p>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-between items-center mb-8">
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">
                    @if(request('status') == 'active')
                        Ativos ({{ $qrCodes->count() }})
                    @elseif(request('status') == 'archived')
                        Pausados ({{ $qrCodes->count() }})
                    @else
                        Todos ({{ $qrCodes->count() }})
                    @endif
                </span>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <button class="btn-outline flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Criar em massa
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            
            <a href="{{ route('qrcodes.create') }}" class="btn-teal flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Criar Código QR
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="flex items-center space-x-4 mb-6">
        <!-- Status Filter -->
        <select name="status" class="form-input w-auto" onchange="filterByStatus(this.value)">
            <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>Todos os Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativo</option>
            <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Pausado</option>
        </select>
        
        <!-- Type Filter -->
        <select name="type" class="form-input w-auto" onchange="filterByType(this.value)">
            <option value="all" {{ request('type') == 'all' || !request('type') ? 'selected' : '' }}>Todos os Tipos</option>
            <option value="url" {{ request('type') == 'url' ? 'selected' : '' }}>Website</option>
            <option value="text" {{ request('type') == 'text' ? 'selected' : '' }}>Texto</option>
            <option value="email" {{ request('type') == 'email' ? 'selected' : '' }}>Email</option>
        </select>
        
        <!-- Order Filter -->
        <select name="order" class="form-input w-auto" onchange="filterByOrder(this.value)">
            <option value="created_at" {{ request('order') == 'created_at' || !request('order') ? 'selected' : '' }}>Último Criado</option>
            <option value="name" {{ request('order') == 'name' ? 'selected' : '' }}>Nome</option>
            <option value="scan_count" {{ request('order') == 'scan_count' ? 'selected' : '' }}>Mais Escaneados</option>
        </select>
        
        <div class="flex-1 max-w-md">
            <form method="GET" class="relative">
                <input type="text" name="search" placeholder="Buscar Códigos QR" 
                       value="{{ request('search') }}" class="form-input pl-10">
                <svg class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <!-- Preserve other filters -->
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                @if(request('type'))
                    <input type="hidden" name="type" value="{{ request('type') }}">
                @endif
                @if(request('order'))
                    <input type="hidden" name="order" value="{{ request('order') }}">
                @endif
            </form>
        </div>
    </div>

    <!-- QR Codes List -->
    <div class="space-y-4">
        @forelse($qrCodes as $qrcode)
        <div class="qr-code-card">
            <div class="flex items-start space-x-4">
                <!-- Checkbox -->
                <div class="flex-shrink-0 pt-1">
                    <input type="checkbox" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                </div>
                
                <!-- QR Code Image -->
                <div class="flex-shrink-0">
                    <div class="w-24 h-24 bg-white border border-gray-200 rounded-lg flex items-center justify-center">
                        @if($qrcode->file_path && \Storage::disk('public')->exists($qrcode->file_path))
                            <img src="{{ \Storage::url($qrcode->file_path) }}" alt="QR Code" class="w-20 h-20 object-contain">
                        @else
                            <div class="w-20 h-20 bg-gray-100 rounded flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 3h6v6H3V3zm8 0h6v6h-6V3zM3 11h6v6H3v-6zm8 0h6v6h-6v-6z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <button onclick="openDownloadModal({{ $qrcode->id }}, '{{ $qrcode->name }}')" class="w-full mt-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-medium py-2 px-3 rounded-md transition duration-200 flex items-center justify-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        BAIXAR
                    </button>
                </div>
                
                <!-- QR Code Details -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="text-xs font-medium text-gray-500">Website</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $qrcode->name }}</h3>
                            
                            <div class="space-y-1 text-sm text-gray-600">
                                @if($qrcode->url)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                    <span class="url-truncate" title="{{ $qrcode->url }}">{{ $qrcode->url }}</span>
                                </div>
                                @endif
                                
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $qrcode->created_at->format('M d, Y') }}</span>
                                </div>
                                
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                    </svg>
                                    <span>{{ $qrcode->folder ? $qrcode->folder->name : 'Sem Pasta' }}</span>
                                </div>
                            </div>
                            
                            <!-- Action Icons -->
                            <div class="flex items-center space-x-3 mt-3">
                                <button class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"/>
                                    </svg>
                                </button>
                                <a href="{{ route('qrcodes.edit', $qrcode) }}" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <button class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                                <form method="POST" action="{{ route('qrcodes.destroy', $qrcode) }}" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este QR Code?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Statistics and Status -->
                        <div class="flex-shrink-0 text-right">
                            <div class="mb-4">
                                <div class="text-2xl font-bold text-blue-600">{{ $qrcode->scans_count ?? 0 }}</div>
                                <div class="text-sm text-gray-500">Varreduras Totais</div>
                                <a href="{{ route('qrcodes.scans', $qrcode) }}" class="text-xs text-blue-600 hover:text-blue-800">Estatísticas →</a>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500">Estado:</span>
                                <div class="flex items-center space-x-1">
                                    <div class="relative">
                                        <input type="checkbox" class="sr-only" {{ $qrcode->is_active ? 'checked' : '' }}>
                                        <div class="w-10 h-6 bg-gray-200 rounded-full shadow-inner"></div>
                                        <div class="absolute w-4 h-4 bg-white rounded-full shadow top-1 left-1 transition-transform"></div>
                                    </div>
                                    <span class="text-sm font-medium {{ $qrcode->status === 'active' ? 'text-green-600' : 'text-gray-500' }}">
                                        {{ $qrcode->status === 'active' ? 'Ativo' : 'Pausado' }}
                                    </span>
                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum QR Code</h3>
            <p class="mt-1 text-sm text-gray-500">Comece criando seu primeiro QR Code.</p>
            <div class="mt-6">
                <a href="{{ route('qrcodes.create') }}" class="btn-teal">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Criar Primeiro QR Code
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($qrCodes->hasPages())
        <div class="mt-8">
        {{ $qrCodes->links() }}
    </div>
    @endif
</div>

<!-- Download Modal -->
<div id="downloadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50" onclick="closeDownloadModal()">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" onclick="event.stopPropagation()">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Baixar QR Code</h3>
                <button onclick="closeDownloadModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div class="space-y-4">
                <!-- QR Code Preview -->
                <div class="flex justify-center">
                    <div id="modalQrPreview" class="w-32 h-32 bg-white border border-gray-200 rounded-lg flex items-center justify-center">
                        <!-- QR Code preview will be loaded here -->
                    </div>
                </div>
                
                <!-- Download Options -->
                <div class="space-y-3">
                    <h4 class="text-sm font-medium text-gray-700">Escolha o formato:</h4>
                    
                    <!-- PNG Option -->
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer" onclick="downloadQrCode('png')">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">PNG</div>
                                <div class="text-xs text-gray-500">Imagem de alta qualidade</div>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    
                    <!-- SVG Option -->
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer" onclick="downloadQrCode('svg')">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">SVG</div>
                                <div class="text-xs text-gray-500">Vetorial, escalável</div>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    
                    <!-- JPG Option -->
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer" onclick="downloadQrCode('jpg')">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">JPG</div>
                                <div class="text-xs text-gray-500">Imagem compacta</div>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button onclick="closeDownloadModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Filter by Status
function filterByStatus(status) {
    const url = new URL(window.location);
    if (status === 'all') {
        url.searchParams.delete('status');
    } else {
        url.searchParams.set('status', status);
    }
    window.location.href = url.toString();
}

// Filter by Type
function filterByType(type) {
    const url = new URL(window.location);
    if (type === 'all') {
        url.searchParams.delete('type');
    } else {
        url.searchParams.set('type', type);
    }
    window.location.href = url.toString();
}

// Filter by Order
function filterByOrder(order) {
    const url = new URL(window.location);
    url.searchParams.set('order', order);
    window.location.href = url.toString();
}

// Download Modal Functions
let currentQrCodeId = null;

function openDownloadModal(qrCodeId, qrCodeName) {
    currentQrCodeId = qrCodeId;
    document.getElementById('modalTitle').textContent = `Baixar ${qrCodeName}`;
    document.getElementById('downloadModal').classList.remove('hidden');
    
    // Load QR Code preview
    loadQrPreview(qrCodeId);
    
    // Check if GD extension is available
    checkGdExtension();
}

function closeDownloadModal() {
    document.getElementById('downloadModal').classList.add('hidden');
    currentQrCodeId = null;
}

function downloadQrCode(format) {
    if (currentQrCodeId) {
        window.open(`/qrcodes/${currentQrCodeId}/download/${format}`, '_blank');
        closeDownloadModal();
    }
}

function loadQrPreview(qrCodeId) {
    const previewDiv = document.getElementById('modalQrPreview');
    
    // Show loading state
    previewDiv.innerHTML = `
        <div class="w-24 h-24 bg-gray-100 rounded flex items-center justify-center">
            <svg class="w-6 h-6 text-gray-400 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
        </div>
    `;
    
    // Load the actual QR code image
    console.log('Loading QR preview for ID:', qrCodeId);
    fetch(`/qrcodes/${qrCodeId}/preview`)
        .then(response => {
            console.log('Preview response status:', response.status);
            console.log('Preview response headers:', response.headers);
            if (response.ok) {
                return response.text();
            }
            throw new Error(`Failed to load preview: ${response.status}`);
        })
        .then(html => {
            console.log('Preview HTML received:', html);
            previewDiv.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading QR preview:', error);
            // Fallback to placeholder
            previewDiv.innerHTML = `
                <div class="w-24 h-24 bg-gray-100 rounded flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 3h6v6H3V3zm8 0h6v6h-6V3zM3 11h6v6H3v-6zm8 0h6v6h-6v-6z"/>
                    </svg>
                </div>
            `;
        });
}

// Check GD extension availability
function checkGdExtension() {
    fetch('/api/check-gd-extension')
        .then(response => response.json())
        .then(data => {
            if (!data.gd_available) {
                // Hide PNG and JPG options if GD is not available
                const pngOption = document.querySelector('[onclick="downloadQrCode(\'png\')"]');
                const jpgOption = document.querySelector('[onclick="downloadQrCode(\'jpg\')"]');
                
                if (pngOption) {
                    pngOption.style.display = 'none';
                }
                if (jpgOption) {
                    jpgOption.style.display = 'none';
                }
                
                // Show message about SVG only
                const downloadOptions = document.querySelector('.space-y-3');
                if (downloadOptions) {
                    const message = document.createElement('div');
                    message.className = 'p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700';
                    message.innerHTML = '⚠️ Apenas formato SVG está disponível (extensão GD não habilitada)';
                    downloadOptions.insertBefore(message, downloadOptions.firstChild);
                }
            }
        })
        .catch(error => {
            console.log('Could not check GD extension, showing all options');
        });
}

// Close modal with ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDownloadModal();
    }
});
</script>
@endsection