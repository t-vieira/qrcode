@extends('layouts.app')

@section('title', 'Criar QR Code')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Criar QR Code</h1>
            <p class="mt-2 text-gray-600">Crie um QR Code personalizado com suas informações</p>
        </div>

        <form action="{{ route('qrcodes.store') }}" method="POST" enctype="multipart/form-data" x-data="qrCodeForm">
            @csrf
            
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <!-- Tipo de QR Code -->
                    <div class="mb-6">
                        <label for="type" class="form-label">Tipo de QR Code</label>
                        <select name="type" id="type" x-model="qrType" @change="updateContent()" class="form-input" required>
                            @foreach($qrTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nome -->
                    <div class="mb-6">
                        <label for="name" class="form-label">Nome do QR Code</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-input" placeholder="Ex: Meu QR Code" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pasta -->
                    <div class="mb-6">
                        <label for="folder_id" class="form-label">Pasta (opcional)</label>
                        <select name="folder_id" id="folder_id" class="form-input">
                            <option value="">Sem pasta</option>
                            @foreach($folders as $folder)
                                <option value="{{ $folder->id }}" {{ old('folder_id') == $folder->id ? 'selected' : '' }}>
                                    {{ $folder->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('folder_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- URL Curta Personalizada -->
                    <div class="mb-6">
                        <label for="short_code" class="form-label">URL Curta Personalizada (opcional)</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                {{ config('app.url') }}/
                            </span>
                            <input type="text" name="short_code" id="short_code" value="{{ old('short_code') }}" 
                                   class="form-input rounded-l-none" placeholder="meu-qr-code">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Deixe em branco para gerar automaticamente</p>
                        @error('short_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- QR Code Dinâmico -->
                    @if(auth()->user()->canAccessAdvancedFeatures())
                        <div class="mb-6">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_dynamic" id="is_dynamic" value="1" 
                                       {{ old('is_dynamic') ? 'checked' : '' }} class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <label for="is_dynamic" class="ml-2 block text-sm text-gray-900">
                                    QR Code Dinâmico
                                </label>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">QR Codes dinâmicos permitem editar o conteúdo sem alterar o código físico</p>
                        </div>
                    @endif

                    <!-- Conteúdo baseado no tipo -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Conteúdo</h3>
                        <div id="content-fields">
                            <!-- Campos serão preenchidos via JavaScript -->
                        </div>
                    </div>

                    <!-- Customização Visual -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Customização Visual</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Cores -->
                            <div>
                                <label for="foregroundColor" class="form-label">Cor do QR Code</label>
                                <input type="color" name="design[foregroundColor]" id="foregroundColor" 
                                       x-model="design.foregroundColor" class="form-input h-10">
                            </div>
                            
                            <div>
                                <label for="backgroundColor" class="form-label">Cor de Fundo</label>
                                <input type="color" name="design[backgroundColor]" id="backgroundColor" 
                                       x-model="design.backgroundColor" class="form-input h-10">
                            </div>
                        </div>

                        <!-- Resolução -->
                        <div class="mt-4">
                            <label for="resolution" class="form-label">Resolução</label>
                            <select name="resolution" id="resolution" x-model="design.resolution" class="form-input">
                                <option value="300">300x300px (Padrão)</option>
                                <option value="500">500x500px (Alta)</option>
                                <option value="1000">1000x1000px (Muito Alta)</option>
                                <option value="2000">2000x2000px (Impressão)</option>
                            </select>
                        </div>

                        <!-- Formato -->
                        <div class="mt-4">
                            <label for="format" class="form-label">Formato</label>
                            <select name="format" id="format" class="form-input">
                                <option value="png">PNG (Recomendado)</option>
                                <option value="jpg">JPG</option>
                                <option value="svg">SVG (Vetorial)</option>
                                <option value="eps">EPS (Impressão)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Preview</h3>
                        <div class="flex justify-center">
                            <div id="qr-preview" class="border-2 border-dashed border-gray-300 rounded-lg p-8 w-64 h-64 flex items-center justify-center">
                                <p class="text-gray-500">Preview aparecerá aqui</p>
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('qrcodes.index') }}" class="btn-outline">
                            Cancelar
                        </a>
                        <button type="submit" class="btn-primary">
                            Criar QR Code
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('[x-data="qrCodeForm"]');
    const typeSelect = document.getElementById('type');
    const contentFields = document.getElementById('content-fields');
    
    // Mapear tipos para campos
    const typeFields = {
        url: [
            { name: 'url', type: 'url', label: 'URL do Site', placeholder: 'https://exemplo.com', required: true }
        ],
        vcard: [
            { name: 'firstName', type: 'text', label: 'Nome', placeholder: 'João' },
            { name: 'lastName', type: 'text', label: 'Sobrenome', placeholder: 'Silva' },
            { name: 'organization', type: 'text', label: 'Empresa', placeholder: 'Minha Empresa Ltda' },
            { name: 'title', type: 'text', label: 'Cargo', placeholder: 'Gerente de Vendas' },
            { name: 'phone', type: 'tel', label: 'Telefone', placeholder: '(11) 99999-9999' },
            { name: 'email', type: 'email', label: 'E-mail', placeholder: 'joao@empresa.com' },
            { name: 'website', type: 'url', label: 'Website', placeholder: 'https://empresa.com' }
        ],
        text: [
            { name: 'text', type: 'textarea', label: 'Texto', placeholder: 'Digite o texto que aparecerá no QR Code...', required: true, rows: 5 }
        ],
        email: [
            { name: 'to', type: 'email', label: 'E-mail Destinatário', placeholder: 'destinatario@exemplo.com', required: true },
            { name: 'subject', type: 'text', label: 'Assunto', placeholder: 'Assunto do e-mail' },
            { name: 'body', type: 'textarea', label: 'Mensagem', placeholder: 'Conteúdo do e-mail', rows: 4 }
        ],
        phone: [
            { name: 'number', type: 'tel', label: 'Número do Telefone', placeholder: '(11) 99999-9999', required: true }
        ],
        sms: [
            { name: 'number', type: 'tel', label: 'Número do Telefone', placeholder: '(11) 99999-9999', required: true },
            { name: 'message', type: 'textarea', label: 'Mensagem', placeholder: 'Digite sua mensagem...', required: true, rows: 3 }
        ],
        wifi: [
            { name: 'ssid', type: 'text', label: 'Nome da Rede (SSID)', placeholder: 'MinhaRede', required: true },
            { name: 'password', type: 'password', label: 'Senha', placeholder: 'senha123' },
            { name: 'security', type: 'select', label: 'Segurança', options: [
                { value: 'WPA', text: 'WPA/WPA2' },
                { value: 'WEP', text: 'WEP' },
                { value: 'nopass', text: 'Sem senha' }
            ] }
        ]
    };
    
    function updateContentFields() {
        const selectedType = typeSelect.value;
        const fields = typeFields[selectedType] || [];
        
        contentFields.innerHTML = '';
        
        fields.forEach(field => {
            const div = document.createElement('div');
            div.className = 'mb-4';
            
            const label = document.createElement('label');
            label.className = 'form-label';
            label.textContent = field.label;
            if (field.required) {
                label.innerHTML += ' <span class="text-red-500">*</span>';
            }
            
            let input;
            if (field.type === 'textarea') {
                input = document.createElement('textarea');
                input.rows = field.rows || 3;
            } else if (field.type === 'select') {
                input = document.createElement('select');
                field.options.forEach(option => {
                    const opt = document.createElement('option');
                    opt.value = option.value;
                    opt.textContent = option.text;
                    input.appendChild(opt);
                });
            } else {
                input = document.createElement('input');
                input.type = field.type;
            }
            
            input.name = `content[${field.name}]`;
            input.id = field.name;
            input.className = 'form-input';
            input.placeholder = field.placeholder;
            if (field.required) {
                input.required = true;
            }
            
            div.appendChild(label);
            div.appendChild(input);
            contentFields.appendChild(div);
        });
    }
    
    // Atualizar campos quando o tipo mudar
    typeSelect.addEventListener('change', updateContentFields);
    
    // Inicializar campos
    updateContentFields();
});
</script>
@endsection
