@extends('layouts.app')

@section('title', 'Criar QR Code')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 text-center">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-primary-600 to-primary-700 bg-clip-text text-transparent">
                Criar QR Code
            </h1>
            <p class="mt-2 text-gray-600">Escolha o tipo de QR Code e personalize seu conteúdo</p>
        </div>

        <form method="POST" action="{{ route('qrcodes.store') }}" x-data="qrCodeForm" @submit="validateForm">
                    @csrf
                    
            <!-- Nome do QR Code -->
                    <div class="mb-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome do QR Code
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               placeholder="Ex: Meu QR Code"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                </div>
                    </div>

            <!-- Seleção de Tipo -->
                    <div class="mb-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Escolha o tipo de QR Code</h2>
                    
                    <!-- Cards de Tipos -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <!-- URL Card -->
                        <div class="qr-type-card" data-type="url">
                            <input type="radio" name="type" value="url" id="type_url" class="hidden" {{ old('type') == 'url' ? 'checked' : '' }}>
                            <label for="type_url" class="block cursor-pointer">
                                <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4 hover:border-blue-400 hover:shadow-md transition-all duration-200 group">
                                    <div class="text-center">
                                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center mx-auto mb-2">
                                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M10.59 13.41c.41.39.41 1.03 0 1.42-.39.39-1.03.39-1.42 0a5.003 5.003 0 0 1 0-7.07l3.54-3.54a5.003 5.003 0 0 1 7.07 0 5.003 5.003 0 0 1 0 7.07l-1.49 1.49c.01-.82-.12-1.64-.4-2.42l.47-.48a2.982 2.982 0 0 0 0-4.24 2.982 2.982 0 0 0-4.24 0l-1.49 1.49.01-.01A2.982 2.982 0 0 0 13 8.05l-1.49 1.49c.39.39.39 1.02 0 1.41-.39.39-1.02.39-1.41 0l-1.49-1.49a2.982 2.982 0 0 0-4.24 0 2.982 2.982 0 0 0 0 4.24l1.49 1.49c-.39.39-.39 1.02 0 1.41.39.39 1.02.39 1.41 0l1.49-1.49a2.982 2.982 0 0 0 4.24 0 2.982 2.982 0 0 0 0-4.24l-1.49-1.49c-.39-.39-1.02-.39-1.41 0z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-sm font-medium text-gray-900 mb-1">URL</h3>
                                        <p class="text-xs text-gray-600">Link para websites</p>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- vCard Card -->
                        <div class="qr-type-card" data-type="vcard">
                            <input type="radio" name="type" value="vcard" id="type_vcard" class="hidden" {{ old('type') == 'vcard' ? 'checked' : '' }}>
                            <label for="type_vcard" class="block cursor-pointer">
                                <div class="bg-green-50 border-2 border-green-200 rounded-lg p-4 hover:border-green-400 hover:shadow-md transition-all duration-200 group">
                                    <div class="text-center">
                                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center mx-auto mb-2">
                                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-sm font-medium text-gray-900 mb-1">vCard</h3>
                                        <p class="text-xs text-gray-600">Informações de contato</p>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Texto Card -->
                        <div class="qr-type-card" data-type="text">
                            <input type="radio" name="type" value="text" id="type_text" class="hidden" {{ old('type') == 'text' ? 'checked' : '' }}>
                            <label for="type_text" class="block cursor-pointer">
                                <div class="bg-purple-50 border-2 border-purple-200 rounded-lg p-4 hover:border-purple-400 hover:shadow-md transition-all duration-200 group">
                                    <div class="text-center">
                                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center mx-auto mb-2">
                                            <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-sm font-medium text-gray-900 mb-1">Texto</h3>
                                        <p class="text-xs text-gray-600">Mensagem simples</p>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Email Card -->
                        <div class="qr-type-card" data-type="email">
                            <input type="radio" name="type" value="email" id="type_email" class="hidden" {{ old('type') == 'email' ? 'checked' : '' }}>
                            <label for="type_email" class="block cursor-pointer">
                                <div class="bg-orange-50 border-2 border-orange-200 rounded-lg p-4 hover:border-orange-400 hover:shadow-md transition-all duration-200 group">
                                    <div class="text-center">
                                        <div class="w-8 h-8 bg-orange-500 rounded-md flex items-center justify-center mx-auto mb-2">
                                            <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-sm font-medium text-gray-900 mb-1">Email</h3>
                                        <p class="text-xs text-gray-600">Abrir cliente de email</p>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Telefone Card -->
                        <div class="qr-type-card" data-type="phone">
                            <input type="radio" name="type" value="phone" id="type_phone" class="hidden" {{ old('type') == 'phone' ? 'checked' : '' }}>
                            <label for="type_phone" class="block cursor-pointer">
                                <div class="bg-teal-50 border-2 border-teal-200 rounded-lg p-4 hover:border-teal-400 hover:shadow-md transition-all duration-200 group">
                                    <div class="text-center">
                                        <div class="w-8 h-8 bg-teal-500 rounded-md flex items-center justify-center mx-auto mb-2">
                                            <svg class="w-6 h-6 text-teal-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-sm font-medium text-gray-900 mb-1">Telefone</h3>
                                        <p class="text-xs text-gray-600">Fazer ligação</p>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- SMS Card -->
                        <div class="qr-type-card" data-type="sms">
                            <input type="radio" name="type" value="sms" id="type_sms" class="hidden" {{ old('type') == 'sms' ? 'checked' : '' }}>
                            <label for="type_sms" class="block cursor-pointer">
                                <div class="bg-pink-50 border-2 border-pink-200 rounded-lg p-4 hover:border-pink-400 hover:shadow-md transition-all duration-200 group">
                                    <div class="text-center">
                                        <div class="w-8 h-8 bg-pink-500 rounded-md flex items-center justify-center mx-auto mb-2">
                                            <svg class="w-6 h-6 text-pink-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-sm font-medium text-gray-900 mb-1">SMS</h3>
                                        <p class="text-xs text-gray-600">Enviar mensagem</p>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Wi-Fi Card -->
                        <div class="qr-type-card" data-type="wifi">
                            <input type="radio" name="type" value="wifi" id="type_wifi" class="hidden" {{ old('type') == 'wifi' ? 'checked' : '' }}>
                            <label for="type_wifi" class="block cursor-pointer">
                                <div class="bg-indigo-50 border-2 border-indigo-200 rounded-lg p-4 hover:border-indigo-400 hover:shadow-md transition-all duration-200 group">
                                    <div class="text-center">
                                        <div class="w-8 h-8 bg-indigo-500 rounded-md flex items-center justify-center mx-auto mb-2">
                                            <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M1,9L3,11L5,9L7,11L9,9L11,11L13,9L15,11L17,9L19,11L21,9V7L19,5L17,7L15,5L13,7L11,5L9,7L7,5L5,7L3,5L1,7V9M1,15L3,17L5,15L7,17L9,15L11,17L13,15L15,17L17,15L19,17L21,15V13L19,11L17,13L15,11L13,13L11,11L9,13L7,11L5,13L3,11L1,13V15Z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-sm font-medium text-gray-900 mb-1">Wi-Fi</h3>
                                        <p class="text-xs text-gray-600">Conectar à rede</p>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Localização Card -->
                        <div class="qr-type-card" data-type="location">
                            <input type="radio" name="type" value="location" id="type_location" class="hidden" {{ old('type') == 'location' ? 'checked' : '' }}>
                            <label for="type_location" class="block cursor-pointer">
                                <div class="bg-red-50 border-2 border-red-200 rounded-lg p-4 hover:border-red-400 hover:shadow-md transition-all duration-200 group">
                                    <div class="text-center">
                                        <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center mx-auto mb-2">
                                            <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22S19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-sm font-medium text-gray-900 mb-1">Localização</h3>
                                        <p class="text-xs text-gray-600">Abrir no mapa</p>
                                    </div>
                                </div>
                        </label>
                        </div>
                    </div>

                    @error('type')
                        <p class="mt-4 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

                    <!-- Conteúdo do QR Code -->
                    <div class="mb-6">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                Conteúdo do QR Code
                        </label>
                        <textarea id="content" 
                                  name="content" 
                                  rows="4"
                                     class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 resize-none"
                                     placeholder="Digite o conteúdo do QR Code aqui..."
                                  required>{{ old('content') }}</textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        </div>
                    </div>

                    <!-- Personalização Visual -->
                    <div class="mb-6">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Personalização Visual</h2>
                            
                            <!-- Templates -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Escolha um template</label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <template x-for="(template, key) in templates" :key="key">
                                        <div @click="selectTemplate(key)" 
                                             class="cursor-pointer p-3 border-2 rounded-lg transition-all duration-200"
                                             :class="selectedTemplate === key ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300'">
                                            <div class="text-center">
                                                <div class="w-12 h-12 mx-auto mb-2 rounded-lg flex items-center justify-center text-white font-bold text-sm"
                                                     :style="`background: ${template.colors.body}; color: ${template.colors.background}`">
                                                    QR
                                                </div>
                                                <div class="text-xs font-medium" x-text="template.name"></div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Cores Personalizadas -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Cores Personalizadas</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Cor do Corpo</label>
                                        <div class="flex items-center space-x-2">
                                            <input type="color" 
                                                   x-model="design.colors.body" 
                                                   class="w-8 h-8 border border-gray-300 rounded cursor-pointer">
                                            <input type="text" 
                                                   x-model="design.colors.body" 
                                                   class="flex-1 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-primary-500">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Cor de Fundo</label>
                                        <div class="flex items-center space-x-2">
                                            <input type="color" 
                                                   x-model="design.colors.background" 
                                                   class="w-8 h-8 border border-gray-300 rounded cursor-pointer">
                                            <input type="text" 
                                                   x-model="design.colors.background" 
                                                   class="flex-1 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-primary-500">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tamanho e Margem -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Dimensões</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Tamanho</label>
                                        <select x-model="design.size" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-primary-500">
                                            <option value="200">200x200 (Pequeno)</option>
                                            <option value="300">300x300 (Médio)</option>
                                            <option value="400">400x400 (Grande)</option>
                                            <option value="500">500x500 (Muito Grande)</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Margem</label>
                                        <select x-model="design.margin" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-primary-500">
                                            <option value="5">5px (Mínima)</option>
                                            <option value="10">10px (Pequena)</option>
                                            <option value="15">15px (Média)</option>
                                            <option value="20">20px (Grande)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Formato -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Formato dos Módulos</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all duration-200"
                                           :class="design.shape === 'square' ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300'">
                                        <input type="radio" x-model="design.shape" value="square" class="sr-only">
                                        <div class="text-center w-full">
                                            <div class="w-6 h-6 mx-auto mb-1 bg-gray-800 rounded-sm"></div>
                                            <div class="text-xs font-medium">Quadrado</div>
                                        </div>
                                    </label>
                                    
                                    <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all duration-200"
                                           :class="design.shape === 'rounded' ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300'">
                                        <input type="radio" x-model="design.shape" value="rounded" class="sr-only">
                                        <div class="text-center w-full">
                                            <div class="w-6 h-6 mx-auto mb-1 bg-gray-800 rounded-md"></div>
                                            <div class="text-xs font-medium">Arredondado</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Preview -->
                            <div class="border-t pt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Preview</label>
                                <div class="flex justify-center">
                                    <div class="w-32 h-32 bg-gray-100 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300">
                                        <div class="w-24 h-24 flex items-center justify-center text-white font-bold text-lg rounded-lg"
                                             :style="`background: ${design.colors.body}; color: ${design.colors.background}; border-radius: ${design.shape === 'rounded' ? '8px' : '0px'};`">
                                            QR
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

            <!-- Campos ocultos para design -->
            <input type="hidden" name="design[colors][body]" :value="design.colors.body">
            <input type="hidden" name="design[colors][background]" :value="design.colors.background">
            <input type="hidden" name="design[size]" :value="design.size">
            <input type="hidden" name="design[margin]" :value="design.margin">
            <input type="hidden" name="design[shape]" :value="design.shape">

            <!-- Botões de Ação -->
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                        <a href="{{ route('qrcodes.index') }}" 
                   class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 text-center">
                            Cancelar
                        </a>
                        <button type="submit" 
                        class="w-full sm:w-auto px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Criar QR Code
                        </button>
                    </div>
                </form>
            </div>
        </div>

<style>
    /* Garantir que todos os cards tenham cores consistentes */
    .qr-type-card[data-type="url"] label > div {
        background: #dbeafe !important;
        border-color: #93c5fd !important;
    }
    
    .qr-type-card[data-type="vcard"] label > div {
        background: #dcfce7 !important;
        border-color: #86efac !important;
    }
    
    .qr-type-card[data-type="text"] label > div {
        background: #f3e8ff !important;
        border-color: #c4b5fd !important;
    }
    
    .qr-type-card[data-type="email"] label > div {
        background: #fed7aa !important;
        border-color: #fdba74 !important;
    }
    
    .qr-type-card[data-type="phone"] label > div {
        background: #ccfbf1 !important;
        border-color: #5eead4 !important;
    }
    
    .qr-type-card[data-type="sms"] label > div {
        background: #fce7f3 !important;
        border-color: #f9a8d4 !important;
    }
    
    .qr-type-card[data-type="wifi"] label > div {
        background: #e0e7ff !important;
        border-color: #a5b4fc !important;
    }
    
    .qr-type-card[data-type="location"] label > div {
        background: #fee2e2 !important;
        border-color: #fca5a5 !important;
    }
    
    /* Seleção elegante - apenas borda e sombra */
    .qr-type-card input[type="radio"]:checked + label > div {
        border-width: 3px !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2), 0 4px 12px rgba(59, 130, 246, 0.15) !important;
        transform: translateY(-1px) !important;
    }
    
    /* Manter cores originais dos ícones */
    .qr-type-card input[type="radio"]:checked + label > div > div:first-child {
        background: inherit !important;
    }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('qrCodeForm', () => ({
            selectedTemplate: 'classic',
            design: {
                colors: {
                    body: '#000000',
                    background: '#ffffff'
                },
                size: 300,
                margin: 10,
                shape: 'square'
            },
            templates: {
                classic: {
                    name: 'Clássico',
                    colors: { body: '#000000', background: '#ffffff' },
                    size: 300,
                    margin: 10,
                    shape: 'square'
                },
                modern: {
                    name: 'Moderno',
                    colors: { body: '#3b82f6', background: '#f8fafc' },
                    size: 300,
                    margin: 15,
                    shape: 'rounded'
                },
                dark: {
                    name: 'Escuro',
                    colors: { body: '#ffffff', background: '#1f2937' },
                    size: 300,
                    margin: 10,
                    shape: 'square'
                },
                colorful: {
                    name: 'Colorido',
                    colors: { body: '#8b5cf6', background: '#fef3c7' },
                    size: 300,
                    margin: 12,
                    shape: 'rounded'
                }
            },
            
            validateForm(event) {
                const selectedType = document.querySelector('input[name="type"]:checked');
                if (!selectedType) {
                    event.preventDefault();
                    alert('Por favor, selecione um tipo de QR Code');
                    return false;
                }
                return true;
            },
            
            selectTemplate(templateKey) {
                this.selectedTemplate = templateKey;
                this.design = { ...this.templates[templateKey] };
            }
        }))
    });
</script>
@endsection