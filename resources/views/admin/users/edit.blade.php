@extends('layouts.admin')

@section('title', 'Editar Usuário')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Editar Usuário</h1>
                        <p class="mt-2 text-gray-600">Atualize as informações do usuário</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.users') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow rounded-lg">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')
                
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informações Básicas</h3>
                </div>
                
                <div class="px-6 py-4 space-y-6">
                    <!-- Nome -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $user->name) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-300 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('email') border-red-300 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status da Assinatura -->
                    <div>
                        <label for="subscription_status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status da Assinatura <span class="text-red-500">*</span>
                        </label>
                        <select id="subscription_status" 
                                name="subscription_status"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('subscription_status') border-red-300 @enderror">
                            <option value="active" {{ old('subscription_status', $user->subscription_status) === 'active' ? 'selected' : '' }}>Ativo</option>
                            <option value="trialing" {{ old('subscription_status', $user->subscription_status) === 'trialing' ? 'selected' : '' }}>Em Teste</option>
                            <option value="inactive" {{ old('subscription_status', $user->subscription_status) === 'inactive' ? 'selected' : '' }}>Inativo</option>
                        </select>
                        @error('subscription_status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Data de Expiração do Trial -->
                    <div>
                        <label for="trial_ends_at" class="block text-sm font-medium text-gray-700 mb-2">
                            Data de Expiração do Trial
                        </label>
                        <input type="datetime-local" 
                               id="trial_ends_at" 
                               name="trial_ends_at" 
                               value="{{ old('trial_ends_at', $user->trial_ends_at ? $user->trial_ends_at->format('Y-m-d\TH:i') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('trial_ends_at') border-red-300 @enderror">
                        @error('trial_ends_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Roles -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Roles</label>
                        <div class="space-y-2">
                            @foreach($roles as $role)
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="roles[]" 
                                           value="{{ $role->name }}"
                                           {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">{{ ucfirst($role->name) }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('roles')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Botões -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                    <a href="{{ route('admin.users') }}" class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
