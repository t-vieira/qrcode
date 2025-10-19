<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - QRFlux</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900" rel="stylesheet" />

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
    
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .hero-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        /* Fix para z-index do menu */
        nav {
            z-index: 9999 !important;
            position: sticky !important;
            top: 0 !important;
            background: white !important;
            background-color: white !important;
        }
        
        /* Garantir que o conteúdo não passe por cima do menu */
        main {
            position: relative;
            z-index: 1;
        }
        
        /* Fix para sobreposição de elementos no header */
        .header-content {
            position: relative;
            z-index: 10;
        }
        
        /* Garantir que cards do header não se sobreponham */
        .header-card {
            position: relative;
            z-index: 5;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        /* Fix para elementos que podem estar causando sobreposição */
        .dashboard-header {
            overflow: visible;
            position: relative;
        }
        
        /* Garantir que texto não se sobreponha */
        .header-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Fix específico para dashboard header */
        .dashboard-stats {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: nowrap;
        }
        
        .dashboard-stats .stat-card {
            flex-shrink: 0;
            min-width: 0;
            position: relative;
            z-index: 10;
        }
        
        /* Garantir que elementos não se sobreponham */
        .dashboard-header .flex {
            gap: 1rem;
        }
        
        .dashboard-header .flex > * {
            flex-shrink: 0;
        }
        
        /* Fix específico para sobreposição de logo */
        .logo-container {
            position: relative;
            z-index: 1;
            max-width: 200px;
            flex-shrink: 0;
        }
        
        .main-content-header {
            position: relative;
            z-index: 2;
            flex: 1;
            min-width: 0;
        }
        
        .user-menu-container {
            position: relative;
            z-index: 3;
            flex-shrink: 0;
        }
        
        /* Garantir que o header principal não tenha sobreposição */
        .main-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            position: relative;
        }
        
        .main-header > * {
            position: relative;
            z-index: auto;
        }
        
        /* Fix para elementos que podem estar causando sobreposição */
        .header-logo {
            position: relative;
            z-index: 1;
            margin-right: 1rem;
        }
        
        .header-title {
            position: relative;
            z-index: 2;
            flex: 1;
            margin: 0 1rem;
        }
        
        .header-user {
            position: relative;
            z-index: 3;
            margin-left: 1rem;
        }
        
        /* Fix crítico para sobreposição de elementos */
        .navbar-container {
            position: relative;
            z-index: 1000;
            background: white;
            width: 100%;
        }
        
        .navbar-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            position: relative;
            z-index: 1001;
        }
        
        .navbar-left {
            position: relative;
            z-index: 1002;
            flex-shrink: 0;
            max-width: 300px;
        }
        
        .navbar-center {
            position: relative;
            z-index: 1003;
            flex: 1;
            min-width: 0;
            margin: 0 2rem;
        }
        
        .navbar-right {
            position: relative;
            z-index: 1004;
            flex-shrink: 0;
        }
        
        /* Garantir que não haja sobreposição */
        .navbar-left * {
            position: relative;
            z-index: 1005;
        }
        
        .navbar-center * {
            position: relative;
            z-index: 1006;
        }
        
        .navbar-right * {
            position: relative;
            z-index: 1007;
        }
        
        /* Garantir fundo branco sólido no header */
        .navbar-container {
            background: white !important;
            background-color: white !important;
        }
        
        .navbar-content {
            background: white !important;
            background-color: white !important;
        }
        
        .navbar-left,
        .navbar-center,
        .navbar-right {
            background: white !important;
            background-color: white !important;
        }
        
        /* Garantir que todos os elementos do header tenham fundo branco */
        nav * {
            background-color: white !important;
        }
        
        /* Exceção para elementos que precisam de cor específica */
        nav .bg-gradient-to-r,
        nav .bg-primary-500,
        nav .bg-primary-600,
        nav .bg-primary-700 {
            background-color: inherit !important;
        }
        
        /* Remover CSS conflitante - deixar Alpine.js controlar */
    </style>
</head>
<body class="font-sans antialiased bg-gray-50" x-data="app">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm sticky top-0 z-[9999] relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-primary-600 to-primary-700 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <h1 class="text-xl font-bold bg-gradient-to-r from-primary-600 to-primary-700 bg-clip-text text-transparent">
                                QRFlux
                            </h1>
                        </div>

                        <!-- Navigation Links -->
                        @auth
                        <div class="hidden space-x-8 sm:ml-10 sm:flex">
                            <a href="{{ route('dashboard') }}" 
                               class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                                </svg>
                                Dashboard
                            </a>
                            <a href="{{ route('qrcodes.index') }}" 
                               class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('qrcodes.*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                QR Codes
                            </a>
                        </div>
                        @endauth
                    </div>

                    <!-- User Menu -->
                    @auth
                    <div class="flex items-center space-x-4">
                        <!-- User Dropdown -->
                        <div class="relative" x-data="userMenu">
                            <button @click="open = !open" type="button" class="flex items-center space-x-3 bg-white rounded-lg px-3 py-2 shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                                <div class="h-8 w-8 rounded-full bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center">
                                        <span class="text-sm font-medium text-white">
                                            {{ substr(auth()->user()->name, 0, 1) }}
                                        </span>
                                    </div>
                                <div class="text-left">
                                    <div class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-gray-500">{{ auth()->user()->email }}</div>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                                </button>

                            <div x-show="open" @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="user-dropdown origin-top-right absolute right-0 mt-2 w-56 rounded-xl shadow-lg py-2 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <div class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</div>
                                    <div class="text-sm text-gray-500">{{ auth()->user()->email }}</div>
                                </div>
                                <div class="py-1">
                                    <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Seu Perfil
                                    </a>
                                    <a href="{{ route('profile.settings') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Configurações
                                    </a>
                                    <a href="{{ route('subscription.upgrade') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                        Assinatura
                                    </a>
                                @if(auth()->user()->hasRole('admin'))
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Painel Admin
                                        </a>
                                @endif
                                </div>
                                <div class="border-t border-gray-100 py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                        Sair
                                    </button>
                                </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endauth

                    <!-- Mobile menu button -->
                    <div class="flex items-center sm:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="bg-white inline-flex items-center justify-center p-2 rounded-lg text-gray-400 hover:text-gray-500 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500 shadow-sm border border-gray-200">
                            <span class="sr-only">Abrir menu principal</span>
                            <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="sm:hidden bg-white border-t border-gray-200">
                @auth
                <div class="pt-2 pb-3 space-y-1">
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center px-4 py-3 text-base font-medium rounded-lg mx-2 {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('qrcodes.index') }}" 
                       class="flex items-center px-4 py-3 text-base font-medium rounded-lg mx-2 {{ request()->routeIs('qrcodes.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        QR Codes
                    </a>
                </div>
                @endauth
                @auth
                <div class="pt-4 pb-3 border-t border-gray-200">
                    <div class="flex items-center px-4">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center">
                                <span class="text-sm font-medium text-white">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800">{{ auth()->user()->name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-50">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Seu Perfil
                        </a>
                        <a href="{{ route('profile.settings') }}" class="flex items-center px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-50">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Configurações
                        </a>
                        <a href="{{ route('subscription.upgrade') }}" class="flex items-center px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-50">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            Assinatura
                        </a>
                        @if(auth()->user()->hasRole('admin'))
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-50">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Painel Admin
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center w-full px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-50">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Sair
                            </button>
                        </form>
                    </div>
                </div>
                @endauth
            </div>
        </nav>

        <!-- Page Content -->
        <main class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-primary-50">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 text-green-700 px-4 py-3 rounded-r-lg relative mx-4 mt-4 shadow-sm" role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="block sm:inline font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-400 text-red-700 px-4 py-3 rounded-r-lg relative mx-4 mt-4 shadow-sm" role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="block sm:inline font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        // Inicializar Alpine.js com estado global
        document.addEventListener('alpine:init', () => {
            Alpine.data('app', () => ({
                mobileMenuOpen: false,
            }))
            
            Alpine.data('userMenu', () => ({
                open: false,
                init() {
                    // Garantir que comece fechado
                    this.open = false;
                }
            }))
        })
        
        // Fix para menu do usuário - garantir que comece fechado
        document.addEventListener('DOMContentLoaded', function() {
            // Aguardar Alpine.js carregar
            setTimeout(() => {
                const userMenus = document.querySelectorAll('[x-data*="userMenu"]');
                userMenus.forEach((menu) => {
                    const alpineData = Alpine.$data(menu);
                    if (alpineData && alpineData.open !== undefined) {
                        alpineData.open = false;
                    }
                });
            }, 1000);
        });
    </script>
</body>
</html>
