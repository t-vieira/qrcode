@extends('layouts.app')

@section('title', 'Criar QR Code')

@section('content')
<div class="p-6">
        <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <a href="{{ route('dashboard') }}" class="flex items-center text-teal-600 hover:text-teal-700 mr-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Dashboard
            </a>
        </div>
        
        <div class="flex items-center mb-4">
            <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
            <h1 class="text-2xl font-bold text-gray-900">Website QR Code Type</h1>
        </div>

        <!-- Progress Steps -->
        <div class="flex items-center space-x-4">
            <div class="flex items-center">
                <div id="step-1" class="w-8 h-8 bg-teal-600 text-white rounded-full flex items-center justify-center text-sm font-medium">1</div>
                <span class="ml-2 text-sm font-medium text-teal-600">Setup Info</span>
            </div>
            <div class="w-8 h-0.5 bg-gray-300"></div>
            <div class="flex items-center">
                <div id="step-2" class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-medium">2</div>
                <span id="step-2-text" class="ml-2 text-sm font-medium text-gray-500">Design QR Code</span>
            </div>
        </div>
    </div>

    <div id="step-1-content" class="max-w-4xl">
        <form id="qr-form" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    @csrf
                    
            <!-- Left Column - Form -->
            <div class="space-y-6">
                <!-- QR Code Name -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome do QR Code
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                           class="form-input"
                           placeholder="Ex: Loja no ifood"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                </div>

                <!-- URL Input -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <label for="url" class="block text-sm font-medium text-gray-700 mb-2">
                        URL do Website
                    </label>
                    <input type="url" 
                           id="url" 
                           name="url" 
                           value="{{ old('url') }}"
                           class="form-input"
                           placeholder="https://www.exemplo.com.br"
                           required>
                    <p class="mt-2 text-sm text-gray-500">
                        Add the website URL to link with your QR Code.
                    </p>
                    @error('url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    </div>

                <!-- Additional Options -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-4">Opções Adicionais</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Descrição (opcional)
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="3"
                                      class="form-input"
                                      placeholder="Descrição do QR Code...">{{ old('description') }}</textarea>
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1" 
                                       class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Ativar QR Code imediatamente</span>
                            </label>
                        </div>
                                    </div>
                                </div>
                        </div>

            <!-- Right Column - Preview -->
            <div class="space-y-6">
                <!-- Preview Card -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Pré-visualização</h3>
                        <button type="button" id="refresh-preview" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                        </button>
                        </div>

                    <!-- Mobile Preview -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mx-auto" style="max-width: 300px;">
                            <!-- Browser Bar -->
                            <div class="flex items-center space-x-2 mb-4">
                                <div class="flex space-x-1">
                                    <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                                    <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                                    <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                                </div>
                                <div class="flex-1 bg-gray-100 rounded px-3 py-1 text-xs text-gray-600" id="preview-url">
                                    https://www.exemplo.com.br
                                </div>
                        </div>

                            <!-- Content -->
                                    <div class="text-center">
                                <div class="w-16 h-16 bg-teal-100 rounded-lg mx-auto mb-4 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-teal-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3 3h6v6H3V3zm8 0h6v6h-6V3zM3 11h6v6H3v-6zm8 0h6v6h-6v-6z"/>
                                            </svg>
                                </div>
                                <p class="text-sm text-gray-600">
                                    The QR code will take you to the URL address.
                                </p>
                                </div>
                        </div>
                    </div>

                    <!-- QR Code Preview -->
                    <div class="text-center">
                        <div class="w-32 h-32 bg-white border-2 border-gray-200 rounded-lg mx-auto flex items-center justify-center">
                            <div id="qr-preview" class="w-24 h-24 bg-gray-100 rounded flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 3h6v6H3V3zm8 0h6v6h-6V3zM3 11h6v6H3v-6zm8 0h6v6h-6v-6z"/>
                                </svg>
            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">QR Code será gerado após salvar</p>
                        </div>
                    </div>

                <!-- Download Options -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Opções de Download</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Formato do arquivo</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="format" value="png" class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300" checked>
                                    <span class="ml-2 text-sm text-gray-700">PNG</span>
                                </label>
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="format" value="svg" class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">SVG</span>
                                </label>
                                        </div>
                                    </div>
                                    
                                    <div>
                            <label for="size" class="block text-sm font-medium text-gray-700 mb-2">Tamanho</label>
                            <select id="size" name="size" class="form-input">
                                <option value="256">256 x 256</option>
                                <option value="512" selected>512 x 512</option>
                                <option value="1024">1024 x 1024</option>
                                <option value="2048">2048 x 2048</option>
                            </select>
                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4 mt-8">
            <a href="{{ route('dashboard') }}" class="btn-outline">
                Cancelar
            </a>
            <button type="button" id="next-step" class="btn-teal">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Criar QR Code
            </button>
                                </div>
                            </div>

    <!-- Step 2 Content (Design) - Hidden by default -->
    <div id="step-2-content" class="max-w-4xl hidden">
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Design do QR Code</h3>
            <p class="text-gray-600 mb-6">Personalize a aparência do seu QR Code</p>
            
            <!-- Design Options -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cor do QR Code</label>
                    <input type="color" id="qr-color" value="#000000" class="w-full h-10 border border-gray-300 rounded-md">
                                        </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cor de fundo</label>
                    <input type="color" id="bg-color" value="#ffffff" class="w-full h-10 border border-gray-300 rounded-md">
                                </div>
                            </div>

            <!-- Final Preview -->
            <div class="mt-6 text-center">
                <div class="w-48 h-48 bg-white border-2 border-gray-200 rounded-lg mx-auto flex items-center justify-center">
                    <div id="final-qr-preview" class="w-40 h-40 bg-gray-100 rounded flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 3h6v6H3V3zm8 0h6v6h-6V3zM3 11h6v6H3v-6zm8 0h6v6h-6v-6z"/>
                        </svg>
                            </div>
                        </div>
                    </div>

            <!-- Final Action Buttons -->
            <div class="flex justify-end space-x-4 mt-8">
                <button type="button" id="back-step" class="btn-outline">
                    Voltar
                </button>
                <button type="button" id="save-qr" class="btn-teal">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Salvar QR Code
                        </button>
            </div>
        </div>
            </div>
        </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const step1Content = document.getElementById('step-1-content');
    const step2Content = document.getElementById('step-2-content');
    const nextStepBtn = document.getElementById('next-step');
    const backStepBtn = document.getElementById('back-step');
    const saveQrBtn = document.getElementById('save-qr');
    const step1 = document.getElementById('step-1');
    const step2 = document.getElementById('step-2');
    const step2Text = document.getElementById('step-2-text');
    
    const nameInput = document.getElementById('name');
    const urlInput = document.getElementById('url');
    const previewUrl = document.getElementById('preview-url');
    const qrPreview = document.getElementById('qr-preview');
    const finalQrPreview = document.getElementById('final-qr-preview');
    const qrColor = document.getElementById('qr-color');
    const bgColor = document.getElementById('bg-color');
    
    // Update preview in real-time
    function updatePreview() {
        const url = urlInput.value || 'https://www.exemplo.com.br';
        previewUrl.textContent = url.length > 30 ? url.substring(0, 30) + '...' : url;
        
        // Update QR code preview with real QR code
        if (urlInput.value) {
            generateQrPreview(url);
        } else {
            qrPreview.innerHTML = `
                <div class="w-20 h-20 bg-gray-100 rounded flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 3h6v6H3V3zm8 0h6v6h-6V3zM3 11h6v6H3v-6zm8 0h6v6h-6v-6z"/>
                    </svg>
                </div>
            `;
        }
    }
    
    // Generate real QR code preview
    function generateQrPreview(url) {
        qrPreview.innerHTML = `
            <div class="w-20 h-20 bg-gray-100 rounded flex items-center justify-center">
                <svg class="w-6 h-6 text-gray-400 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
        `;
        
        // Generate QR code using a simple API or library
        fetch('/api/generate-qr-preview', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                url: url,
                size: 80
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.qr_code_url) {
                qrPreview.innerHTML = `
                    <img src="${data.qr_code_url}" alt="QR Code Preview" class="w-20 h-20 object-contain">
                `;
            } else {
                // Fallback to simple QR code using qr.js library
                generateSimpleQrCode(url);
            }
        })
        .catch(error => {
            console.error('Error generating QR preview:', error);
            // Fallback to simple QR code
            generateSimpleQrCode(url);
        });
    }
    
    // Generate simple QR code using qr.js library
    function generateSimpleQrCode(url) {
        // Create a simple QR code using canvas
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = 80;
        canvas.height = 80;
        
        // Simple QR code pattern (placeholder)
        ctx.fillStyle = '#000000';
        ctx.fillRect(10, 10, 60, 60);
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(15, 15, 50, 50);
        ctx.fillStyle = '#000000';
        ctx.fillRect(20, 20, 40, 40);
        
        qrPreview.innerHTML = `
            <img src="${canvas.toDataURL()}" alt="QR Code Preview" class="w-20 h-20 object-contain">
        `;
    }
    
    // Update final QR preview
    function updateFinalPreview() {
        const qrColorValue = qrColor.value;
        const bgColorValue = bgColor.value;
        
        finalQrPreview.innerHTML = `
            <div class="w-36 h-36 rounded" style="background-color: ${bgColorValue};">
                <div class="w-full h-full flex items-center justify-center">
                    <div class="w-32 h-32 rounded" style="background-color: ${qrColorValue};">
                        <svg class="w-24 h-24 text-white mx-auto mt-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 3h6v6H3V3zm8 0h6v6h-6V3zM3 11h6v6H3v-6zm8 0h6v6h-6v-6z"/>
                        </svg>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Event listeners
    urlInput.addEventListener('input', updatePreview);
    nameInput.addEventListener('input', updatePreview);
    qrColor.addEventListener('change', updateFinalPreview);
    bgColor.addEventListener('change', updateFinalPreview);
    
    // Step navigation
    nextStepBtn.addEventListener('click', function() {
        // Validate required fields
        if (!nameInput.value || !urlInput.value) {
            alert('Por favor, preencha todos os campos obrigatórios.');
            return;
        }
        
        // Show step 2
        step1Content.classList.add('hidden');
        step2Content.classList.remove('hidden');
        
        // Update step indicators
        step1.classList.remove('bg-teal-600', 'text-white');
        step1.classList.add('bg-gray-300', 'text-gray-600');
        step2.classList.remove('bg-gray-300', 'text-gray-600');
        step2.classList.add('bg-teal-600', 'text-white');
        step2Text.classList.remove('text-gray-500');
        step2Text.classList.add('text-teal-600');
        
        // Update final preview
        updateFinalPreview();
    });
    
    backStepBtn.addEventListener('click', function() {
        // Show step 1
        step2Content.classList.add('hidden');
        step1Content.classList.remove('hidden');
        
        // Update step indicators
        step2.classList.remove('bg-teal-600', 'text-white');
        step2.classList.add('bg-gray-300', 'text-gray-600');
        step1.classList.remove('bg-gray-300', 'text-gray-600');
        step1.classList.add('bg-teal-600', 'text-white');
        step2Text.classList.remove('text-teal-600');
        step2Text.classList.add('text-gray-500');
    });
    
    // Save QR Code
    saveQrBtn.addEventListener('click', function() {
        // Disable button to prevent double submission
        saveQrBtn.disabled = true;
        saveQrBtn.textContent = 'Salvando...';
        
        // Create form data with correct field names
        const formData = new FormData();
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        formData.append('name', nameInput.value);
        formData.append('type', 'url'); // QR Code type
        formData.append('content', urlInput.value); // URL content
        formData.append('description', document.getElementById('description').value);
        formData.append('is_active', document.querySelector('input[name="is_active"]').checked ? '1' : '0');
        
        // Design data
        const design = {
            colors: {
                body: qrColor.value,
                background: bgColor.value
            },
            size: parseInt(document.getElementById('size').value),
            margin: 10,
            shape: 'square'
        };
        formData.append('design', JSON.stringify(design));
        
        // Submit form
        fetch('{{ route("qrcodes.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            
            if (response.status === 200) {
                // Try to parse as JSON first
                return response.json().catch(() => {
                    // If not JSON, assume success and redirect
                    console.log('Response is not JSON, assuming success');
                    return { success: true };
                });
            } else {
                throw new Error('HTTP ' + response.status);
            }
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success !== false) {
                // Success - redirect to dashboard
                window.location.href = '{{ route("dashboard") }}';
            } else {
                alert('Erro ao criar QR Code: ' + (data.message || 'Erro desconhecido'));
                saveQrBtn.disabled = false;
                saveQrBtn.textContent = 'Salvar QR Code';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao criar QR Code: ' + error.message);
            saveQrBtn.disabled = false;
            saveQrBtn.textContent = 'Salvar QR Code';
        });
    });
    
    // Initial preview update
    updatePreview();
    });
</script>
@endsection