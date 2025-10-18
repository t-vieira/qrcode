@extends('layouts.app')

@section('title', 'Meu Perfil')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Meu Perfil</h1>
            <p class="mt-2 text-gray-600">Gerencie suas informações pessoais e configurações de conta</p>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="text-center">
                        <div class="mx-auto h-20 w-20 rounded-full bg-primary-500 flex items-center justify-center mb-4">
                            <span class="text-2xl font-bold text-white">
                                {{ substr($user->name, 0, 1) }}
                            </span>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        
                        @if($user->email_verified_at)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                                Email verificado
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mt-2">
                                Email não verificado
                            </span>
                        @endif
                    </div>

                    <div class="mt-6 space-y-2">
                        <a href="{{ route('profile.edit') }}" class="block w-full text-center px-4 py-2 text-sm font-medium text-primary-600 bg-primary-50 rounded-md hover:bg-primary-100 transition-colors">
                            Editar Perfil
                        </a>
                        <a href="{{ route('profile.password.edit') }}" class="block w-full text-center px-4 py-2 text-sm font-medium text-gray-600 bg-gray-50 rounded-md hover:bg-gray-100 transition-colors">
                            Alterar Senha
                        </a>
                        <a href="{{ route('profile.settings') }}" class="block w-full text-center px-4 py-2 text-sm font-medium text-gray-600 bg-gray-50 rounded-md hover:bg-gray-100 transition-colors">
                            Configurações
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informações Pessoais -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Informações Pessoais</h2>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nome completo</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Membro desde</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Última atualização</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('d/m/Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Status da Assinatura -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Status da Assinatura</h2>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Status atual</p>
                            <p class="text-lg font-medium text-gray-900">
                                @if($user->subscription_status === 'active')
                                    <span class="text-green-600">Ativo</span>
                                @elseif($user->subscription_status === 'trialing')
                                    <span class="text-blue-600">Período de teste</span>
                                @else
                                    <span class="text-gray-600">Inativo</span>
                                @endif
                            </p>
                        </div>
                        <div class="text-right">
                            @if($user->trial_ends_at && $user->subscription_status === 'trialing')
                                <p class="text-sm text-gray-500">Teste expira em</p>
                                <p class="text-lg font-medium text-gray-900">
                                    {{ $user->trial_ends_at->diffForHumans() }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('subscription.upgrade') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                            Gerenciar Assinatura
                        </a>
                    </div>
                </div>

                <!-- Estatísticas -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Suas Estatísticas</h2>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">QR Codes criados</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $user->qrCodes()->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total de scans</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">
                                {{ $user->qrCodes()->withCount('scans')->get()->sum('scans_count') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Pastas criadas</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $user->folders()->count() }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
