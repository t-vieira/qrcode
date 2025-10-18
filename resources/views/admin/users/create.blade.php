@extends('layouts.app')

@section('title', 'Criar Usuário')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <a href="{{ route('admin.users') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Criar Usuário</h1>
                    <p class="mt-2 text-gray-600">Adicione um novo usuário ao sistema</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <!-- Success Message -->
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Nome -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nome completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-300 @enderror"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('email') border-red-300 @enderror"
                           required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Senha -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Senha <span class="text-red-500">*</span>
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('password') border-red-300 @enderror"
                           required>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        A senha deve ter pelo menos 8 caracteres.
                    </p>
                </div>

                <!-- Confirmar Senha -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirmar senha <span class="text-red-500">*</span>
                    </label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                           required>
                </div>

                <!-- Status da Assinatura -->
                <div class="mb-6">
                    <label for="subscription_status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status da Assinatura <span class="text-red-500">*</span>
                    </label>
                    <select id="subscription_status" 
                            name="subscription_status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('subscription_status') border-red-300 @enderror"
                            required>
                        <option value="">Selecione um status</option>
                        <option value="active" {{ old('subscription_status') === 'active' ? 'selected' : '' }}>Ativo</option>
                        <option value="trialing" {{ old('subscription_status') === 'trialing' ? 'selected' : '' }}>Em Teste</option>
                        <option value="inactive" {{ old('subscription_status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
                    </select>
                    @error('subscription_status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Data de Expiração do Trial -->
                <div class="mb-6" id="trial_date_field" style="display: none;">
                    <label for="trial_ends_at" class="block text-sm font-medium text-gray-700 mb-2">
                        Data de expiração do teste
                    </label>
                    <input type="datetime-local" 
                           id="trial_ends_at" 
                           name="trial_ends_at"
                           value="{{ old('trial_ends_at') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('trial_ends_at') border-red-300 @enderror">
                    @error('trial_ends_at')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Roles -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Roles
                    </label>
                    <div class="space-y-2">
                        @foreach($roles as $role)
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="roles[]" 
                                       value="{{ $role->name }}"
                                       {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">{{ ucfirst($role->name) }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('roles')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botões -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.users') }}" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        Criar Usuário
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const subscriptionStatus = document.getElementById('subscription_status');
    const trialDateField = document.getElementById('trial_date_field');
    
    function toggleTrialDateField() {
        if (subscriptionStatus.value === 'trialing') {
            trialDateField.style.display = 'block';
        } else {
            trialDateField.style.display = 'none';
        }
    }
    
    subscriptionStatus.addEventListener('change', toggleTrialDateField);
    toggleTrialDateField(); // Executar na carga inicial
});
</script>
@endsection
