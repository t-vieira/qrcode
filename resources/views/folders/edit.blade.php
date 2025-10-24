@extends('layouts.app')

@section('title', 'Editar Pasta')

@section('content')
<div class="py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <a href="{{ route('folders.index') }}" 
                   class="mr-4 p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Editar Pasta</h1>
                    <p class="text-gray-600 mt-1">{{ $folder->name }}</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <form action="{{ route('folders.update', $folder) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                
                <!-- Folder Name -->
                <div class="mb-6">
                    <label for="name" class="form-label">Nome da Pasta</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $folder->name) }}"
                           class="form-input @error('name') border-red-300 @enderror"
                           placeholder="Ex: Marketing, Eventos, Produtos..."
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Parent Folder -->
                <div class="mb-6">
                    <label for="parent_id" class="form-label">Pasta Pai</label>
                    <select id="parent_id" 
                            name="parent_id" 
                            class="form-input @error('parent_id') border-red-300 @enderror">
                        <option value="">Nenhuma (Pasta Raiz)</option>
                        @foreach($parentFolders as $parentFolder)
                        <option value="{{ $parentFolder->id }}" 
                                {{ old('parent_id', $folder->parent_id) == $parentFolder->id ? 'selected' : '' }}>
                            {{ $parentFolder->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        Selecione uma pasta pai para mover esta pasta
                    </p>
                </div>

                <!-- Current Path -->
                @if($folder->parent)
                <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                    <h3 class="text-sm font-medium text-blue-900 mb-1">Localização Atual</h3>
                    <p class="text-sm text-blue-700">{{ $folder->path }}</p>
                </div>
                @endif

                <!-- Preview -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Preview</h3>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900" id="preview-name">
                                {{ old('name', $folder->name) }}
                            </div>
                            <div class="text-xs text-gray-500" id="preview-path">
                                @if(old('parent_id') || $folder->parent_id)
                                    @php
                                        $selectedParentId = old('parent_id', $folder->parent_id);
                                        $selectedParent = $parentFolders->find($selectedParentId);
                                    @endphp
                                    @if($selectedParent)
                                        {{ $selectedParent->name }} / {{ old('name', $folder->name) }}
                                    @else
                                        {{ old('name', $folder->name) }}
                                    @endif
                                @else
                                    {{ old('name', $folder->name) }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('folders.index') }}" 
                       class="btn-outline">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const parentSelect = document.getElementById('parent_id');
    const previewName = document.getElementById('preview-name');
    const previewPath = document.getElementById('preview-path');

    function updatePreview() {
        const name = nameInput.value || 'Nome da Pasta';
        const parentId = parentSelect.value;
        
        previewName.textContent = name;
        
        if (parentId) {
            const parentOption = parentSelect.options[parentSelect.selectedIndex];
            const parentName = parentOption.textContent;
            previewPath.textContent = `${parentName} / ${name}`;
        } else {
            previewPath.textContent = name;
        }
    }

    nameInput.addEventListener('input', updatePreview);
    parentSelect.addEventListener('change', updatePreview);
});
</script>
@endsection
