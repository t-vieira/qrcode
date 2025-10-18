@extends('layouts.app')

@section('title', 'Meus QR Codes')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Meus QR Codes</h1>
                    <p class="mt-2 text-gray-600">Gerencie todos os seus QR Codes</p>
                </div>
                <a href="{{ route('qrcodes.create') }}" class="btn-primary">
                    Criar QR Code
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-primary-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Ativos</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['active'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h1.586a1 1 0 01.707.293l1.414 1.414a1 1 0 00.707.293H19a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Arquivados</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['archived'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Scans</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_scans'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Codes List -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Lista de QR Codes</h3>
                
                @if($qrCodes->count() > 0)
                    <div class="space-y-4">
                        @foreach($qrCodes as $qrCode)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-12 w-12 bg-gray-200 rounded flex items-center justify-center">
                                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $qrCode->name }}</h4>
                                        <p class="text-sm text-gray-500">
                                            {{ ucfirst($qrCode->type) }} • 
                                            Código: {{ $qrCode->short_code }} • 
                                            {{ $qrCode->created_at->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('qrcodes.show', $qrCode) }}" class="text-primary-600 hover:text-primary-900 text-sm font-medium">
                                        Ver
                                    </a>
                                    <a href="{{ route('qrcodes.edit', $qrCode) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        Editar
                                    </a>
                                    <form method="POST" action="{{ route('qrcodes.destroy', $qrCode) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium" 
                                                onclick="return confirm('Tem certeza que deseja deletar este QR Code?')">
                                            Deletar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $qrCodes->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum QR Code</h3>
                        <p class="mt-1 text-sm text-gray-500">Comece criando seu primeiro QR Code.</p>
                        <div class="mt-6">
                            <a href="{{ route('qrcodes.create') }}" class="btn-primary">
                                Criar QR Code
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection