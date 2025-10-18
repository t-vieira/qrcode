@extends('layouts.admin')

@section('title', 'Detalhes do Usuário')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Detalhes do Usuário</h1>
                        <p class="mt-2 text-gray-600">Informações completas do usuário</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Editar
                        </a>
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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informações do Usuário -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informações do Usuário</h3>
                    </div>
                    <div class="px-6 py-4 space-y-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-16 w-16">
                                <div class="h-16 w-16 rounded-full bg-primary-500 flex items-center justify-center">
                                    <span class="text-xl font-medium text-white">
                                        {{ substr($user->name, 0, 1) }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-xl font-medium text-gray-900">{{ $user->name }}</h4>
                                <p class="text-gray-500">{{ $user->email }}</p>
                                <div class="flex items-center mt-2 space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($user->subscription_status === 'active') bg-green-100 text-green-800
                                        @elseif($user->subscription_status === 'trialing') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($user->subscription_status) }}
                                    </span>
                                    @if($user->hasRole('admin'))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Admin
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Membro desde</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Última atualização</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            @if($user->trial_ends_at)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Trial expira em</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->trial_ends_at->format('d/m/Y H:i') }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email verificado</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($user->email_verified_at)
                                        <span class="text-green-600">✓ Verificado</span>
                                    @else
                                        <span class="text-red-600">✗ Não verificado</span>
                                    @endif
                                </dd>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- QR Codes do Usuário -->
                <div class="mt-8 bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">QR Codes ({{ $user->qrCodes->count() }})</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse($user->qrCodes->take(5) as $qrCode)
                            <div class="px-6 py-4 flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">{{ $qrCode->name }}</p>
                                        <p class="text-sm text-gray-500">{{ ucfirst($qrCode->type) }} • {{ $qrCode->scans->count() }} scans</p>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $qrCode->created_at->format('d/m/Y') }}
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-4 text-center text-gray-500">
                                Nenhum QR Code encontrado
                            </div>
                        @endforelse
                    </div>
                    @if($user->qrCodes->count() > 5)
                        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                            <a href="{{ route('admin.qr-codes') }}?user={{ $user->id }}" class="text-sm text-primary-600 hover:text-primary-900">
                                Ver todos os QR Codes deste usuário →
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Estatísticas -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Estatísticas</h3>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">QR Codes</span>
                            <span class="text-sm font-medium text-gray-900">{{ $user->qrCodes->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Total de Scans</span>
                            <span class="text-sm font-medium text-gray-900">{{ $user->qrCodes->sum(function($qr) { return $qr->scans->count(); }) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Equipes</span>
                            <span class="text-sm font-medium text-gray-900">{{ $user->teams->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Assinaturas</span>
                            <span class="text-sm font-medium text-gray-900">{{ $user->subscriptions->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Ações</h3>
                    </div>
                    <div class="px-6 py-4 space-y-3">
                        <a href="{{ route('admin.users.edit', $user) }}" class="w-full flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Editar Usuário
                        </a>
                        
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full flex items-center justify-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Excluir Usuário
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
