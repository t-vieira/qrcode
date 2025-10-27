@extends('layouts.app')

@section('title', 'Editar QR Code')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Editar QR Code</h1>
            <p class="mt-2 text-gray-600">Edite as informações do seu QR Code</p>
        </div>

        <!-- Form -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form method="POST" action="{{ route('qrcodes.update', $qrCode) }}">
                    @csrf
                    @method('PUT')
                    
                    <!-- Nome -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome do QR Code
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $qrCode->name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                               placeholder="Ex: Meu QR Code"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tipo (readonly) -->
                    <div class="mb-6">
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de QR Code
                        </label>
                        <input type="text" 
                               id="type" 
                               value="{{ ucfirst($qrCode->type) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500"
                               readonly>
                        <p class="mt-1 text-sm text-gray-500">O tipo não pode ser alterado após a criação</p>
                    </div>

                    <!-- Conteúdo -->
                    <div class="mb-6">
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                            Conteúdo
                        </label>
                        <textarea id="content" 
                                  name="content" 
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                                  placeholder="Digite o conteúdo do QR Code..."
                                  required>{{ old('content', $qrCode->content) }}</textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Design (Opcional) -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm font-medium text-gray-700">
                                Design Personalizado (Opcional)
                            </label>
                            <button type="button" 
                                    id="toggle-design"
                                    class="text-sm text-primary-600 hover:text-primary-500">
                                Alterar Design
                            </button>
                        </div>
                        
                        <div id="design-section" class="hidden space-y-4 p-4 border border-gray-200 rounded-md bg-gray-50">
                            <!-- Cor do QR Code -->
                            <div>
                                <label for="design_color" class="block text-sm font-medium text-gray-700 mb-1">
                                    Cor do QR Code
                                </label>
                                <input type="color" 
                                       id="design_color" 
                                       name="design_color"
                                       value="{{ old('design_color', $qrCode->design['colors']['body'] ?? '#000000') }}"
                                       class="w-20 h-10 border border-gray-300 rounded cursor-pointer">
                            </div>
                            
                            <!-- Cor de Fundo -->
                            <div>
                                <label for="design_background" class="block text-sm font-medium text-gray-700 mb-1">
                                    Cor de Fundo
                                </label>
                                <input type="color" 
                                       id="design_background" 
                                       name="design_background"
                                       value="{{ old('design_background', $qrCode->design['colors']['background'] ?? '#ffffff') }}"
                                       class="w-20 h-10 border border-gray-300 rounded cursor-pointer">
                            </div>
                            
                            <!-- Tamanho -->
                            <div>
                                <label for="design_size" class="block text-sm font-medium text-gray-700 mb-1">
                                    Tamanho (px)
                                </label>
                                <input type="number" 
                                       id="design_size" 
                                       name="design_size"
                                       value="{{ old('design_size', $qrCode->design['size'] ?? 300) }}"
                                       min="100" 
                                       max="2000" 
                                       step="50"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            
                            <!-- Forma -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Forma dos Módulos</label>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="radio" 
                                               name="design_shape" 
                                               value="square"
                                               {{ old('design_shape', $qrCode->design['shape'] ?? 'square') === 'square' ? 'checked' : '' }}
                                               class="mr-2">
                                        <span class="text-sm text-gray-700">Quadrados</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" 
                                               name="design_shape" 
                                               value="round"
                                               {{ old('design_shape', $qrCode->design['shape'] ?? 'square') === 'round' ? 'checked' : '' }}
                                               class="mr-2">
                                        <span class="text-sm text-gray-700">Arredondados</span>
                                    </label>
                                </div>
                            </div>
                            
                            <p class="text-xs text-gray-500">
                                ⚠️ Alterar o design regenerará a imagem do QR Code
                            </p>
                        </div>
                    </div>

                    <!-- Informações adicionais -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-md">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Informações do QR Code</h4>
                        <dl class="grid grid-cols-1 gap-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Código Curto:</dt>
                                <dd class="font-mono text-gray-900">{{ $qrCode->short_code }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Status:</dt>
                                <dd class="text-gray-900">{{ ucfirst($qrCode->status) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Tipo:</dt>
                                <dd class="text-gray-900">{{ $qrCode->is_dynamic ? 'Dinâmico' : 'Estático' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Botões -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('qrcodes.show', $qrCode) }}" 
                           class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.getElementById('toggle-design');
    const designSection = document.getElementById('design-section');
    const form = document.querySelector('form');
    
    // Toggle design section
    toggleButton.addEventListener('click', function() {
        if (designSection.classList.contains('hidden')) {
            designSection.classList.remove('hidden');
            toggleButton.textContent = 'Ocultar Design';
        } else {
            designSection.classList.add('hidden');
            toggleButton.textContent = 'Alterar Design';
        }
    });
    
    // Process design data before form submission
    form.addEventListener('submit', function(e) {
        const designData = {
            colors: {
                body: document.getElementById('design_color').value,
                background: document.getElementById('design_background').value
            },
            size: parseInt(document.getElementById('design_size').value),
            shape: document.querySelector('input[name="design_shape"]:checked').value,
            margin: 10
        };
        
        // Create hidden input for design data
        const designInput = document.createElement('input');
        designInput.type = 'hidden';
        designInput.name = 'design';
        designInput.value = JSON.stringify(designData);
        
        form.appendChild(designInput);
    });
});
</script>
@endsection
