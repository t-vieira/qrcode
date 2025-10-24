@extends('layouts.app')

@section('title', $folder->name)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="{{ route('folders.index') }}" 
                       class="mr-4 p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <div class="flex items-center mb-2">
                            <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $folder->name }}</h1>
                                <p class="text-gray-600">{{ $folder->qr_codes_count }} QR {{ $folder->qr_codes_count == 1 ? 'Code' : 'Codes' }}</p>
                            </div>
                        </div>
                        @if($folder->path !== $folder->name)
                        <p class="text-sm text-gray-500">{{ $folder->path }}</p>
                        @endif
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <a href="{{ route('folders.edit', $folder) }}" 
                       class="btn-outline">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar
                    </a>
                    <a href="{{ route('qrcodes.create') }}?folder={{ $folder->id }}" 
                       class="btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Novo QR Code
                    </a>
                </div>
            </div>
        </div>

        <!-- Subfolders -->
        @if($subfolders->count() > 0)
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Subpastas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($subfolders as $subfolder)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900">{{ $subfolder->name }}</h3>
                                    <p class="text-xs text-gray-500">{{ $subfolder->qr_codes_count }} QR {{ $subfolder->qr_codes_count == 1 ? 'Code' : 'Codes' }}</p>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('folders.show', $subfolder) }}" 
                           class="block w-full text-center py-2 px-3 bg-teal-600 text-white text-xs font-medium rounded-md hover:bg-teal-700 transition-colors duration-200">
                            Ver Pasta
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- QR Codes -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">QR Codes</h2>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">{{ $qrCodes->total() }} {{ $qrCodes->total() == 1 ? 'QR Code' : 'QR Codes' }}</span>
                </div>
            </div>

            @if($qrCodes->count() > 0)
            <div class="space-y-4">
                @foreach($qrCodes as $qrcode)
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
                                        @if($qrcode->content && is_array($qrcode->content) && isset($qrcode->content['url']))
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                            </svg>
                                            <span class="url-truncate" title="{{ $qrcode->content['url'] }}">{{ $qrcode->content['url'] }}</span>
                                        </div>
                                        @elseif($qrcode->content && is_string($qrcode->content))
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                            </svg>
                                            <span class="url-truncate" title="{{ $qrcode->content }}">{{ $qrcode->content }}</span>
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
                                            <span>{{ $folder->name }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Icons -->
                                <div class="flex items-center space-x-2">
                                    <button onclick="copyQrCode({{ $qrcode->id }})" 
                                            class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"
                                            title="Copiar QR Code">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                    
                                    <button onclick="duplicateQrCode({{ $qrcode->id }})" 
                                            class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"
                                            title="Duplicar QR Code">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                                        </svg>
                                    </button>
                                    
                                    <a href="{{ route('qrcodes.edit', $qrcode) }}" 
                                       class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"
                                       title="Editar QR Code">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($qrCodes->hasPages())
            <div class="mt-6">
                {{ $qrCodes->links() }}
            </div>
            @endif
            @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h6v6H3V3zm8 0h6v6h-6V3zM3 11h6v6H3v-6zm8 0h6v6h-6v-6z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum QR Code nesta pasta</h3>
                <p class="text-gray-500 mb-6">Crie seu primeiro QR Code para esta pasta.</p>
                <a href="{{ route('qrcodes.create') }}?folder={{ $folder->id }}" 
                   class="btn-primary inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Criar Primeiro QR Code
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
// Copy QR Code
function copyQrCode(qrCodeId) {
    fetch(`/qrcodes/${qrCodeId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.short_code) {
                const qrCodeUrl = `${window.location.origin}/r/${data.short_code}`;
                
                navigator.clipboard.writeText(qrCodeUrl).then(() => {
                    const button = event.target.closest('button');
                    const originalTitle = button.title;
                    button.title = 'Copiado!';
                    button.classList.add('text-green-600');
                    
                    setTimeout(() => {
                        button.title = originalTitle;
                        button.classList.remove('text-green-600');
                    }, 2000);
                }).catch(err => {
                    console.error('Erro ao copiar:', err);
                    alert('Erro ao copiar QR Code');
                });
            } else {
                alert('Erro ao obter URL do QR Code');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao obter informações do QR Code');
        });
}

// Duplicate QR Code
function duplicateQrCode(qrCodeId) {
    if (confirm('Tem certeza que deseja duplicar este QR Code?')) {
        fetch(`/qrcodes/${qrCodeId}/duplicate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao duplicar QR Code');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao duplicar QR Code');
        });
    }
}
</script>
@endsection
