@extends('layouts.app')

@section('title', 'Criar QR Code')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-hidden">
    <!-- Sidebar - QR Code Types -->
    <div class="w-64 bg-white border-r border-gray-200 flex-shrink-0 overflow-y-auto">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Tipos de QR Code</h2>
        </div>
        <nav class="p-2 space-y-1">
            @foreach([
                ['id' => 'url', 'icon' => 'link', 'label' => 'URL Website'],
                ['id' => 'vcard', 'icon' => 'address-card', 'label' => 'Cartão de Visita'],
                ['id' => 'text', 'icon' => 'align-left', 'label' => 'Texto Simples'],
                ['id' => 'email', 'icon' => 'envelope', 'label' => 'E-mail'],
                ['id' => 'sms', 'icon' => 'comment-alt', 'label' => 'SMS'],
                ['id' => 'wifi', 'icon' => 'wifi', 'label' => 'Wi-Fi'],
                ['id' => 'whatsapp', 'icon' => 'whatsapp', 'label' => 'WhatsApp'],
                ['id' => 'event', 'icon' => 'calendar-alt', 'label' => 'Evento'],
                ['id' => 'crypto', 'icon' => 'bitcoin', 'label' => 'Criptomoeda'],
            ] as $type)
            <button type="button" 
                    onclick="selectType('{{ $type['id'] }}')"
                    class="type-btn w-full flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors {{ $loop->first ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}"
                    data-type="{{ $type['id'] }}">
                <i class="fas fa-{{ $type['icon'] }} w-6 text-center mr-2"></i>
                {{ $type['label'] }}
            </button>
            @endforeach
        </nav>
    </div>

    <!-- Main Content - Form & Preview -->
    <div class="flex-1 flex overflow-hidden">
        <!-- Center - Configuration Form -->
        <div class="flex-1 overflow-y-auto p-8">
            <div class="max-w-2xl mx-auto">
                <form id="qr-form" action="{{ route('qrcodes.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type" id="type-input" value="url">
                    <input type="hidden" name="design" id="design-input">
                    
                    <!-- Header -->
                    <div class="mb-8">
                        <h1 class="text-2xl font-bold text-gray-900 mb-2" id="form-title">URL Website</h1>
                        <p class="text-gray-600" id="form-description">Crie um QR Code que redireciona para um site.</p>
                    </div>

                    <!-- Common Fields -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6 shadow-sm">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome do QR Code</label>
                            <input type="text" name="name" class="form-input w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" placeholder="Ex: Meu Site Pessoal" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pasta (Opcional)</label>
                            <select name="folder_id" class="form-select w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                <option value="">Sem pasta</option>
                                @foreach($folders as $folder)
                                    <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Dynamic Content Fields -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6 shadow-sm">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Conteúdo</h3>
                        
                        <!-- URL Form -->
                        <div id="form-url" class="type-form">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">URL do Website</label>
                                <input type="url" name="content_url" class="content-input form-input w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" placeholder="https://www.exemplo.com.br">
                            </div>
                        </div>

                        <!-- VCard Form -->
                        <div id="form-vcard" class="type-form hidden">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                                    <input type="text" id="vcard-first-name" class="vcard-field form-input w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Sobrenome</label>
                                    <input type="text" id="vcard-last-name" class="vcard-field form-input w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                                <input type="tel" id="vcard-phone" class="vcard-field form-input w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                                <input type="email" id="vcard-email" class="vcard-field form-input w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                                <input type="text" id="vcard-company" class="vcard-field form-input w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                                <input type="text" id="vcard-job" class="vcard-field form-input w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Site</label>
                                <input type="url" id="vcard-website" class="vcard-field form-input w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                        </div>

                        <!-- Text Form -->
                        <div id="form-text" class="type-form hidden">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Texto</label>
                                <textarea name="content_text" rows="5" class="content-input form-textarea w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" placeholder="Digite seu texto aqui..."></textarea>
                            </div>
                        </div>

                        <!-- Email Form -->
                        <div id="form-email" class="type-form hidden">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">E-mail de Destino</label>
                                <input type="email" id="email-address" class="email-field form-input w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Assunto</label>
                                <input type="text" id="email-subject" class="email-field form-input w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mensagem</label>
                                <textarea id="email-body" rows="4" class="email-field form-textarea w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"></textarea>
                            </div>
                        </div>

                        <!-- SMS Form -->
                        <div id="form-sms" class="type-form hidden">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Número de Telefone</label>
                                <input type="tel" id="sms-phone" class="sms-field form-input w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mensagem</label>
                                <textarea id="sms-message" rows="4" class="sms-field form-textarea w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"></textarea>
                            </div>
                        </div>

                        <!-- WiFi Form -->
                        <div id="form-wifi" class="type-form hidden">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nome da Rede (SSID)</label>
                                <input type="text" id="wifi-ssid" class="wifi-field form-input w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                                <input type="text" id="wifi-password" class="wifi-field form-input w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Criptografia</label>
                                <select id="wifi-encryption" class="wifi-field form-select w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                    <option value="WPA">WPA/WPA2</option>
                                    <option value="WEP">WEP</option>
                                    <option value="nopass">Sem senha</option>
                                </select>
                            </div>
                        </div>

                        <!-- WhatsApp Form -->
                        <div id="form-whatsapp" class="type-form hidden">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Número (com código do país)</label>
                                <input type="tel" id="whatsapp-phone" class="whatsapp-field form-input w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" placeholder="+5511999999999">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mensagem (Opcional)</label>
                                <textarea id="whatsapp-message" rows="4" class="whatsapp-field form-textarea w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"></textarea>
                            </div>
                        </div>

                        <!-- Event Form -->
                        <div id="form-event" class="type-form hidden">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Título do Evento</label>
                                <input type="text" id="event-title" class="event-field form-input w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Início</label>
                                    <input type="datetime-local" id="event-start" class="event-field form-input w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fim</label>
                                    <input type="datetime-local" id="event-end" class="event-field form-input w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Localização</label>
                                <input type="text" id="event-location" class="event-field form-input w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                        </div>

                        <!-- Crypto Form -->
                        <div id="form-crypto" class="type-form hidden">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Moeda</label>
                                <select id="crypto-currency" class="crypto-field form-select w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                    <option value="bitcoin">Bitcoin</option>
                                    <option value="ethereum">Ethereum</option>
                                    <option value="litecoin">Litecoin</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Endereço da Carteira</label>
                                <input type="text" id="crypto-address" class="crypto-field form-input w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Valor (Opcional)</label>
                                <input type="number" step="any" id="crypto-amount" class="crypto-field form-input w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                        </div>

                        <!-- Hidden Content Input for Submission -->
                        <input type="hidden" name="content" id="final-content">
                    </div>

                    <!-- Design Customization -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6 shadow-sm">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Personalização</h3>
                        
                        <!-- Tabs -->
                        <div class="border-b border-gray-200 mb-4">
                            <nav class="-mb-px flex space-x-8">
                                <button type="button" onclick="switchDesignTab('frames')" class="design-tab-btn border-teal-500 text-teal-600 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm" data-tab="frames">Molduras</button>
                                <button type="button" onclick="switchDesignTab('colors')" class="design-tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm" data-tab="colors">Cores</button>
                                <button type="button" onclick="switchDesignTab('logo')" class="design-tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm" data-tab="logo">Logo</button>
                            </nav>
                        </div>

                        <!-- Frames Tab -->
                        <div id="design-frames" class="design-tab">
                            <div class="grid grid-cols-3 gap-4">
                                <button type="button" onclick="selectFrame('simple')" class="frame-btn p-2 border-2 border-teal-500 rounded-lg hover:bg-gray-50" data-frame="simple">
                                    <div class="aspect-square bg-gray-100 rounded flex items-center justify-center text-xs text-gray-500">Simples</div>
                                </button>
                                <button type="button" onclick="selectFrame('bubble_bottom')" class="frame-btn p-2 border-2 border-transparent rounded-lg hover:bg-gray-50" data-frame="bubble_bottom">
                                    <div class="aspect-square bg-gray-100 rounded flex items-center justify-center text-xs text-gray-500">Balão Baixo</div>
                                </button>
                                <button type="button" onclick="selectFrame('bubble_top')" class="frame-btn p-2 border-2 border-transparent rounded-lg hover:bg-gray-50" data-frame="bubble_top">
                                    <div class="aspect-square bg-gray-100 rounded flex items-center justify-center text-xs text-gray-500">Balão Topo</div>
                                </button>
                                <button type="button" onclick="selectFrame('polaroid')" class="frame-btn p-2 border-2 border-transparent rounded-lg hover:bg-gray-50" data-frame="polaroid">
                                    <div class="aspect-square bg-gray-100 rounded flex items-center justify-center text-xs text-gray-500">Polaroid</div>
                                </button>
                                <button type="button" onclick="selectFrame('phone')" class="frame-btn p-2 border-2 border-transparent rounded-lg hover:bg-gray-50" data-frame="phone">
                                    <div class="aspect-square bg-gray-100 rounded flex items-center justify-center text-xs text-gray-500">Celular</div>
                                </button>
                            </div>
                            
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Texto da Moldura</label>
                                <input type="text" id="frame-label" value="SCAN ME" class="form-input w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            </div>
                        </div>

                        <!-- Colors Tab -->
                        <div id="design-colors" class="design-tab hidden">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cor do QR Code</label>
                                    <input type="color" id="color-body" value="#000000" class="w-full h-10 rounded-md border border-gray-300 p-1">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cor de Fundo</label>
                                    <input type="color" id="color-background" value="#ffffff" class="w-full h-10 rounded-md border border-gray-300 p-1">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cor da Moldura</label>
                                    <input type="color" id="color-frame" value="#000000" class="w-full h-10 rounded-md border border-gray-300 p-1">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cor do Texto</label>
                                    <input type="color" id="color-text" value="#ffffff" class="w-full h-10 rounded-md border border-gray-300 p-1">
                                </div>
                            </div>
                        </div>

                        <!-- Logo Tab -->
                        <div id="design-logo" class="design-tab hidden">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Logo</label>
                                <input type="file" id="logo-upload" name="logo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                            </div>
                            <p class="text-xs text-gray-500">Recomendado: Imagem quadrada, fundo transparente, max 2MB.</p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancelar</a>
                        <button type="submit" class="px-4 py-2 bg-teal-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                            Criar QR Code
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Sidebar - Preview -->
        <div class="w-96 bg-gray-100 border-l border-gray-200 p-8 flex flex-col items-center justify-center sticky top-0 h-screen">
            <div class="bg-white p-4 rounded-xl shadow-lg mb-6 w-full max-w-xs">
                <div class="aspect-[3/4] bg-gray-50 rounded-lg flex items-center justify-center overflow-hidden relative" id="preview-container">
                    <!-- Preview Image will be injected here -->
                    <div id="loading-preview" class="hidden absolute inset-0 bg-white/80 flex items-center justify-center z-10">
                        <i class="fas fa-spinner fa-spin text-teal-600 text-3xl"></i>
                    </div>
                    <img id="qr-preview-image" src="" alt="QR Code Preview" class="max-w-full max-h-full object-contain hidden">
                    <div id="empty-preview" class="text-center text-gray-400">
                        <i class="fas fa-qrcode text-6xl mb-4"></i>
                        <p>Preencha os dados para visualizar</p>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <button type="button" id="refresh-preview-btn" class="text-teal-600 hover:text-teal-700 font-medium text-sm flex items-center justify-center">
                    <i class="fas fa-sync-alt mr-2"></i> Atualizar Preview
                </button>
            </div>
        </div>
    </div>
</div>

<!-- FontAwesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
    // State
    let currentType = 'url';
    let currentFrame = 'simple';
    let designState = {
        colors: { body: '#000000', background: '#ffffff', frame: '#000000', text: '#ffffff' },
        frame: { style: 'simple', label: 'SCAN ME' },
        logo: null
    };

    // Elements
    const form = document.getElementById('qr-form');
    const typeInput = document.getElementById('type-input');
    const designInput = document.getElementById('design-input');
    const finalContentInput = document.getElementById('final-content');
    const previewImage = document.getElementById('qr-preview-image');
    const emptyPreview = document.getElementById('empty-preview');
    const loadingPreview = document.getElementById('loading-preview');
    const logoUpload = document.getElementById('logo-upload');

    // Type Selection
    window.selectType = function(type) {
        currentType = type;
        typeInput.value = type;
        
        // Update UI
        document.querySelectorAll('.type-btn').forEach(btn => {
            if (btn.dataset.type === type) {
                btn.classList.remove('text-gray-700', 'hover:bg-gray-50');
                btn.classList.add('bg-teal-50', 'text-teal-700');
            } else {
                btn.classList.add('text-gray-700', 'hover:bg-gray-50');
                btn.classList.remove('bg-teal-50', 'text-teal-700');
            }
        });

        // Show/Hide Forms
        document.querySelectorAll('.type-form').forEach(f => f.classList.add('hidden'));
        document.getElementById(`form-${type}`).classList.remove('hidden');

        // Update Header
        const labels = {
            'url': 'URL Website', 'vcard': 'Cartão de Visita', 'text': 'Texto Simples',
            'email': 'E-mail', 'sms': 'SMS', 'wifi': 'Wi-Fi',
            'whatsapp': 'WhatsApp', 'event': 'Evento', 'crypto': 'Criptomoeda'
        };
        document.getElementById('form-title').textContent = labels[type];
        
        updatePreview();
    };

    // Design Tabs
    window.switchDesignTab = function(tab) {
        document.querySelectorAll('.design-tab').forEach(t => t.classList.add('hidden'));
        document.getElementById(`design-${tab}`).classList.remove('hidden');
        
        document.querySelectorAll('.design-tab-btn').forEach(btn => {
            if (btn.dataset.tab === tab) {
                btn.classList.remove('border-transparent', 'text-gray-500');
                btn.classList.add('border-teal-500', 'text-teal-600');
            } else {
                btn.classList.add('border-transparent', 'text-gray-500');
                btn.classList.remove('border-teal-500', 'text-teal-600');
            }
        });
    };

    // Frame Selection
    window.selectFrame = function(frame) {
        currentFrame = frame;
        designState.frame.style = frame;
        
        document.querySelectorAll('.frame-btn').forEach(btn => {
            if (btn.dataset.frame === frame) {
                btn.classList.remove('border-transparent');
                btn.classList.add('border-teal-500');
            } else {
                btn.classList.add('border-transparent');
                btn.classList.remove('border-teal-500');
            }
        });
        
        updatePreview();
    };

    // Content Generators
    function getContent() {
        switch(currentType) {
            case 'url':
                return document.querySelector('input[name="content_url"]').value;
            case 'vcard':
                const fname = document.getElementById('vcard-first-name').value;
                const lname = document.getElementById('vcard-last-name').value;
                const phone = document.getElementById('vcard-phone').value;
                const email = document.getElementById('vcard-email').value;
                return `BEGIN:VCARD\nVERSION:3.0\nN:${lname};${fname}\nFN:${fname} ${lname}\nTEL:${phone}\nEMAIL:${email}\nEND:VCARD`;
            case 'text':
                return document.querySelector('textarea[name="content_text"]').value;
            case 'email':
                const eAddr = document.getElementById('email-address').value;
                const eSub = document.getElementById('email-subject').value;
                const eBody = document.getElementById('email-body').value;
                return `mailto:${eAddr}?subject=${encodeURIComponent(eSub)}&body=${encodeURIComponent(eBody)}`;
            case 'sms':
                const sPhone = document.getElementById('sms-phone').value;
                const sMsg = document.getElementById('sms-message').value;
                return `smsto:${sPhone}:${sMsg}`;
            case 'wifi':
                const ssid = document.getElementById('wifi-ssid').value;
                const pass = document.getElementById('wifi-password').value;
                const enc = document.getElementById('wifi-encryption').value;
                return `WIFI:S:${ssid};T:${enc};P:${pass};;`;
            case 'whatsapp':
                const wPhone = document.getElementById('whatsapp-phone').value;
                const wMsg = document.getElementById('whatsapp-message').value;
                return `https://wa.me/${wPhone.replace(/[^0-9]/g, '')}?text=${encodeURIComponent(wMsg)}`;
            case 'event':
                // Simple event format
                return `BEGIN:VEVENT\nSUMMARY:${document.getElementById('event-title').value}\nLOCATION:${document.getElementById('event-location').value}\nDTSTART:${document.getElementById('event-start').value}\nDTEND:${document.getElementById('event-end').value}\nEND:VEVENT`;
            case 'crypto':
                const cAddr = document.getElementById('crypto-address').value;
                const cAmt = document.getElementById('crypto-amount').value;
                const cCurr = document.getElementById('crypto-currency').value;
                return `${cCurr}:${cAddr}?amount=${cAmt}`;
            default:
                return '';
        }
    }

    // Preview Updater
    let debounceTimer;
    function updatePreview() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const content = getContent();
            if (!content || content.length < 5) { // Basic validation
                emptyPreview.classList.remove('hidden');
                previewImage.classList.add('hidden');
                return;
            }

            loadingPreview.classList.remove('hidden');
            
            // Prepare Design Data
            designState.colors.body = document.getElementById('color-body').value;
            designState.colors.background = document.getElementById('color-background').value;
            designState.colors.frame = document.getElementById('color-frame').value;
            designState.colors.text = document.getElementById('color-text').value;
            designState.frame.label = document.getElementById('frame-label').value;
            
            // Construct design object for backend
            const designPayload = {
                colors: {
                    body: designState.colors.body,
                    background: designState.colors.background
                },
                frame: {
                    style: designState.frame.style,
                    label: designState.frame.label,
                    color: designState.colors.frame,
                    text_color: designState.colors.text
                },
                logo: designState.logo
            };

            // Call API
            fetch('{{ route("qrcodes.preview") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    content: content,
                    type: currentType,
                    design: designPayload
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    previewImage.src = data.preview_url;
                    previewImage.classList.remove('hidden');
                    emptyPreview.classList.add('hidden');
                }
            })
            .catch(err => console.error(err))
            .finally(() => {
                loadingPreview.classList.add('hidden');
            });
        }, 500);
    }

    // Event Listeners
    document.querySelectorAll('input, textarea, select').forEach(el => {
        el.addEventListener('input', updatePreview);
        el.addEventListener('change', updatePreview);
    });

    document.getElementById('refresh-preview-btn').addEventListener('click', updatePreview);

    // Logo Upload
    if (logoUpload) {
        logoUpload.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    designState.logo = e.target.result;
                    updatePreview();
                };
                reader.readAsDataURL(file);
            } else {
                designState.logo = null;
                updatePreview();
            }
        });
    }

    // Form Submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        finalContentInput.value = getContent();
        
        // Prepare final design JSON
        const designPayload = {
            colors: {
                body: document.getElementById('color-body').value,
                background: document.getElementById('color-background').value
            },
            frame: {
                style: currentFrame,
                label: document.getElementById('frame-label').value,
                color: document.getElementById('color-frame').value,
                text_color: document.getElementById('color-text').value
            },
            logo: designState.logo
        };
        designInput.value = JSON.stringify(designPayload);
        
        this.submit();
    });

    // Initial Load
    selectType('url');
});
</script>
@endsection