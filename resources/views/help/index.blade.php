@extends('layouts.app')

@section('title', 'Central de Ajuda')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-bold text-gray-900">Central de Ajuda</h1>
            <p class="mt-4 text-xl text-gray-600">Encontre respostas para suas dúvidas e aprenda a usar todas as funcionalidades</p>
        </div>

        <!-- Busca -->
        <div class="mb-12">
            <div class="max-w-2xl mx-auto">
                <div class="relative">
                    <input type="text" 
                           placeholder="Digite sua dúvida aqui..."
                           class="w-full px-4 py-3 pl-12 pr-4 text-lg border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Ajuda -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <!-- FAQ -->
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Perguntas Frequentes</h3>
                </div>
                <p class="text-gray-600 mb-4">Encontre respostas para as dúvidas mais comuns sobre o sistema.</p>
                <a href="{{ route('help.faq') }}" class="inline-flex items-center text-primary-600 hover:text-primary-500 font-medium">
                    Ver FAQ
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <!-- Tutoriais -->
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Tutoriais</h3>
                </div>
                <p class="text-gray-600 mb-4">Aprenda passo a passo como usar todas as funcionalidades.</p>
                <a href="{{ route('help.tutorials') }}" class="inline-flex items-center text-primary-600 hover:text-primary-500 font-medium">
                    Ver Tutoriais
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <!-- Contato -->
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Suporte</h3>
                </div>
                <p class="text-gray-600 mb-4">Precisa de ajuda? Entre em contato conosco via WhatsApp ou email.</p>
                <a href="{{ route('help.contact') }}" class="inline-flex items-center text-primary-600 hover:text-primary-500 font-medium">
                    Entrar em Contato
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Seções Rápidas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Início Rápido -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">🚀 Início Rápido</h3>
                <div class="space-y-3">
                    <a href="{{ route('qrcodes.create') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <span class="w-6 h-6 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center text-sm font-medium mr-3">1</span>
                        <span class="text-gray-700">Criar seu primeiro QR Code</span>
                    </a>
                    <a href="{{ route('dashboard') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <span class="w-6 h-6 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center text-sm font-medium mr-3">2</span>
                        <span class="text-gray-700">Explorar o dashboard</span>
                    </a>
                    <a href="{{ route('help.tutorials') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <span class="w-6 h-6 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center text-sm font-medium mr-3">3</span>
                        <span class="text-gray-700">Aprender funcionalidades avançadas</span>
                    </a>
                </div>
            </div>

            <!-- Recursos Populares -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">⭐ Recursos Populares</h3>
                <div class="space-y-3">
                    <a href="{{ route('help.faq') }}#qrcodes" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="text-gray-700">Tipos de QR Codes disponíveis</span>
                    </a>
                    <a href="{{ route('help.faq') }}#estatisticas" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="text-gray-700">Entender estatísticas e relatórios</span>
                    </a>
                    <a href="{{ route('help.faq') }}#pagamento" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span class="text-gray-700">Informações sobre pagamentos</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Links Legais -->
        <div class="mt-12 pt-8 border-t border-gray-200">
            <div class="flex flex-wrap justify-center gap-6 text-sm text-gray-500">
                <a href="{{ route('help.privacy') }}" class="hover:text-gray-700">Política de Privacidade</a>
                <a href="{{ route('help.terms') }}" class="hover:text-gray-700">Termos de Uso</a>
                <a href="{{ route('privacy.index') }}" class="hover:text-gray-700">Privacidade de Dados</a>
                <a href="{{ route('help.contact') }}" class="hover:text-gray-700">Contato</a>
            </div>
        </div>
    </div>
</div>
@endsection
