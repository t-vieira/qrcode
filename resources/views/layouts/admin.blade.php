<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Painel Administrativo') - QR CODE CREATOR</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900" rel="stylesheet" />

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
    
    <style>
        .sidebar-bg {
            background-color: #f8f9fa;
        }
        .main-content-bg {
            background-color: #ffffff;
        }
        .qr-icon {
            background: linear-gradient(135deg, #14b8a6 0%, #22c55e 100%);
        }
        .active-nav-item {
            background-color: #dcfce7;
            color: #16a34a;
        }
        .nav-item:hover {
            background-color: #f0fdf4;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="flex h-screen">
        <!-- Mobile menu button -->
        <button id="mobile-menu-button" 
                class="lg:hidden fixed top-4 left-4 z-50 bg-teal-600 text-white p-2 rounded-md">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Mobile overlay -->
        <div id="mobile-overlay" 
             class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

        <!-- Sidebar -->
        <div id="sidebar" 
             class="sidebar-bg w-64 lg:w-72 flex-shrink-0 lg:flex lg:flex-col lg:translate-x-0 transform -translate-x-full transition-transform duration-300 ease-in-out lg:transition-none z-50 lg:z-auto">
            
            <!-- Logo -->
            <div class="flex items-center px-6 py-4 border-b border-gray-200">
                <div class="qr-icon w-8 h-8 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 3h6v6H3V3zm8 0h6v6h-6V3zM3 11h6v6H3v-6zm8 0h6v6h-6v-6z"/>
                    </svg>
                </div>
                <h1 class="text-lg font-bold text-gray-900">QR CODE CREATOR</h1>
            </div>

            <!-- User Account -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center cursor-pointer hover:bg-gray-100 rounded-lg p-2 -mx-2">
                    <div class="w-10 h-10 bg-teal-500 rounded-full flex items-center justify-center text-white font-semibold mr-3">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Minha conta</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-6 py-4">
                <div class="space-y-1">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">ADMINISTRAÇÃO</div>
                    
                    <a href="{{ route('admin.dashboard') }}" 
                       class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.dashboard') ? 'active-nav-item' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                        </svg>
                        Dashboard
                    </a>
                    
                    <a href="{{ route('admin.users') }}" 
                       class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.users*') ? 'active-nav-item' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                        Usuários
                    </a>
                    
                    <a href="{{ route('admin.qr-codes') }}" 
                       class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.qr-codes*') ? 'active-nav-item' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        QR Codes
                    </a>
                    
                    <a href="{{ route('admin.statistics') }}" 
                       class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.statistics') ? 'active-nav-item' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Estatísticas
                    </a>
                    
                    <a href="{{ route('admin.subscriptions') }}" 
                       class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.subscriptions*') ? 'active-nav-item' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Assinaturas
                    </a>
                </div>

                <div class="mt-8">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">NAVEGAÇÃO</div>
                    <a href="{{ route('dashboard') }}" 
                       class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                        </svg>
                        Dashboard Usuário
                    </a>
                </div>
            </nav>

            <!-- Trial Info -->
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <p class="text-xs text-yellow-800">
                        Free Trial termina em 5 Dias - 
                        <a href="{{ route('subscription.upgrade') }}" class="text-blue-600 hover:text-blue-800 font-medium">Atualizar</a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <div class="main-content-bg shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-6">
                    <div class="flex items-center">
                        <h2 class="text-lg font-semibold text-gray-900">@yield('title', 'Painel Administrativo')</h2>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('help.index') }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Help
                        </a>
                        
                        <div class="text-sm text-gray-700">
                            <span class="font-medium">{{ auth()->user()->name }}</span>
                            <span class="text-gray-500">(Admin)</span>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">
                                Dashboard Usuário
                            </a>
                            <span class="text-gray-300">|</span>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                                    Sair
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="flex-1 overflow-auto main-content-bg">
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');

            function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }

            mobileMenuButton.addEventListener('click', toggleSidebar);
            overlay.addEventListener('click', closeSidebar);

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth < 1024) {
                    if (!sidebar.contains(event.target) && !mobileMenuButton.contains(event.target)) {
                        closeSidebar();
                    }
                }
            });
        });
    </script>
</body>
</html>