@extends('layouts.app')

@section('title', 'Personalizar QR Code')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Personalizar QR Code</h1>
            <p class="mt-2 text-gray-600">Crie QR Codes únicos com cores, bordas e designs personalizados</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Painel de Design -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-6">Configurações de Design</h2>
                    
                    <form id="designForm" x-data="qrDesigner">
                        <!-- Templates Pré-definidos -->
                        <div class="mb-8">
                            <h3 class="text-sm font-medium text-gray-900 mb-4">Templates</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <template x-for="(template, key) in templates" :key="key">
                                    <div @click="selectTemplate(key)" 
                                         class="cursor-pointer p-4 border-2 rounded-lg transition-all duration-200"
                                         :class="selectedTemplate === key ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300'">
                                        <div class="text-center">
                                            <div class="w-16 h-16 mx-auto mb-2 rounded-lg flex items-center justify-center text-white font-bold"
                                                 :style="`background: ${template.colors.body}; color: ${template.colors.background}`">
                                                QR
                                            </div>
                                            <div class="text-sm font-medium" x-text="template.name"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Cores -->
                        <div class="mb-8">
                            <h3 class="text-sm font-medium text-gray-900 mb-4">Cores</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Cor do Corpo</label>
                                    <div class="flex items-center space-x-3">
                                        <input type="color" 
                                               x-model="design.colors.body" 
                                               class="w-12 h-10 border border-gray-300 rounded-md cursor-pointer">
                                        <input type="text" 
                                               x-model="design.colors.body" 
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Cor de Fundo</label>
                                    <div class="flex items-center space-x-3">
                                        <input type="color" 
                                               x-model="design.colors.background" 
                                               class="w-12 h-10 border border-gray-300 rounded-md cursor-pointer">
                                        <input type="text" 
                                               x-model="design.colors.background" 
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tamanho e Margem -->
                        <div class="mb-8">
                            <h3 class="text-sm font-medium text-gray-900 mb-4">Dimensões</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tamanho (px)</label>
                                    <select x-model="design.size" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                                        <option value="200">200x200 (Pequeno)</option>
                                        <option value="300">300x300 (Médio)</option>
                                        <option value="400">400x400 (Grande)</option>
                                        <option value="500">500x500 (Muito Grande)</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Margem (px)</label>
                                    <select x-model="design.margin" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                                        <option value="5">5px (Mínima)</option>
                                        <option value="10">10px (Pequena)</option>
                                        <option value="15">15px (Média)</option>
                                        <option value="20">20px (Grande)</option>
                                        <option value="30">30px (Muito Grande)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Formato -->
                        <div class="mb-8">
                            <h3 class="text-sm font-medium text-gray-900 mb-4">Formato</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all duration-200"
                                       :class="design.shape === 'square' ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300'">
                                    <input type="radio" x-model="design.shape" value="square" class="sr-only">
                                    <div class="text-center w-full">
                                        <div class="w-8 h-8 mx-auto mb-2 bg-gray-800 rounded-sm"></div>
                                        <div class="text-sm font-medium">Quadrado</div>
                                    </div>
                                </label>
                                
                                <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all duration-200"
                                       :class="design.shape === 'rounded' ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300'">
                                    <input type="radio" x-model="design.shape" value="rounded" class="sr-only">
                                    <div class="text-center w-full">
                                        <div class="w-8 h-8 mx-auto mb-2 bg-gray-800 rounded-md"></div>
                                        <div class="text-sm font-medium">Arredondado</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Logo Central -->
                        <div class="mb-8">
                            <h3 class="text-sm font-medium text-gray-900 mb-4">Logo Central (Opcional)</h3>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-sm text-gray-500">Arraste uma imagem aqui ou clique para selecionar</p>
                                <input type="file" accept="image/*" class="hidden" id="logoUpload">
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="flex justify-between">
                            <a href="{{ route('qrcodes.index') }}" 
                               class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                Cancelar
                            </a>
                            <button type="button" 
                                    @click="generatePreview()"
                                    class="px-6 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                Visualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Preview -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg p-6 sticky top-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Preview</h3>
                    
                    <div class="text-center">
                        <div id="qrPreview" class="w-64 h-64 mx-auto bg-gray-100 rounded-lg flex items-center justify-center mb-4">
                            <div class="text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <p class="text-sm">Configure o design para ver o preview</p>
                            </div>
                        </div>
                        
                        <div class="text-sm text-gray-500">
                            <p>Resolução: <span x-text="design.size"></span>x<span x-text="design.size"></span>px</p>
                            <p>Formato: SVG</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('qrDesigner', () => ({
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
        
        selectTemplate(templateKey) {
            this.selectedTemplate = templateKey;
            this.design = { ...this.templates[templateKey] };
        },
        
        generatePreview() {
            // Simular geração de preview
            const preview = document.getElementById('qrPreview');
            preview.innerHTML = `
                <div class="w-full h-full flex items-center justify-center text-white font-bold text-2xl"
                     style="background: ${this.design.colors.body}; color: ${this.design.colors.background}; border-radius: ${this.design.shape === 'rounded' ? '8px' : '0px'};">
                    QR
                </div>
            `;
        }
    }));
});
</script>
@endsection
