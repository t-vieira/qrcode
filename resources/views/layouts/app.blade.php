<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - QRFlux</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#14b8a6">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="QRFlux">
    <meta name="description" content="A plataforma mais completa para criação, personalização e rastreamento de QR Codes profissionais">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    
    <!-- PWA Icons -->
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('icon-512x512.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icon-192x192.png') }}">

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
        
        /* Mobile layout fixes */
        @media (max-width: 1023px) {
            #sidebar {
                position: fixed !important;
                top: 0;
                left: 0;
                height: 100vh;
                z-index: 50;
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            
            #sidebar:not(.-translate-x-full) {
                transform: translateX(0);
            }
            
            .main-content {
                width: 100% !important;
                margin-left: 0 !important;
            }
        }
        
        @media (min-width: 1024px) {
            #sidebar {
                position: relative !important;
                transform: translateX(0) !important;
            }
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
             class="sidebar-bg w-64 lg:w-72 lg:flex-shrink-0 lg:flex lg:flex-col lg:translate-x-0 transform -translate-x-full transition-transform duration-300 ease-in-out lg:transition-none z-50 lg:z-auto lg:relative fixed lg:static">
            
            <!-- Logo -->
            <div class="flex items-center px-6 py-4 border-b border-gray-200">
                <div class="qr-icon w-8 h-8 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 3h6v6H3V3zm8 0h6v6h-6V3zM3 11h6v6H3v-6zm8 0h6v6h-6v-6z"/>
                    </svg>
                </div>
                <h1 class="text-lg font-bold text-gray-900">QRFlux</h1>
            </div>

            <!-- User Account -->
            @auth
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
            @endauth

            <!-- Navigation -->
            @auth
            <nav class="flex-1 px-6 py-4">
                <div class="space-y-1">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">QR CODES</div>
                    
                    <a href="{{ route('dashboard') }}" 
                       class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'active-nav-item' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        Todos
                    </a>
                    
                    <a href="{{ route('qrcodes.index') }}?status=active" 
                       class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Ativo
                    </a>
                    
                    <a href="{{ route('qrcodes.index') }}?status=archived" 
                       class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Pausado
                    </a>
                    
                    <a href="{{ route('folders.index') }}" 
                       class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('folders.*') ? 'active-nav-item' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                        </svg>
                        Pastas
                    </a>
                </div>

                <div class="mt-8">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Suporte</div>
                    <a href="{{ route('help.index') }}" 
                       class="nav-item flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Centro de Suporte
                    </a>
                </div>
            </nav>
            @endauth

            <!-- Trial Info -->
            @auth
            @if(auth()->user()->shouldShowTrialInfo())
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <p class="text-xs text-yellow-800">
                        Free Trial termina em {{ auth()->user()->getTrialDaysRemaining() }} {{ auth()->user()->getTrialDaysRemaining() == 1 ? 'Dia' : 'Dias' }} - 
                        <a href="{{ route('subscription.upgrade') }}" class="text-blue-600 hover:text-blue-800 font-medium">Atualizar</a>
                    </p>
                </div>
            </div>
            @endif
            @endauth
        </div>

        <!-- Main Content -->
        <div class="main-content flex-1 flex flex-col overflow-hidden w-full lg:w-auto">
            <!-- Top Navigation -->
            <div class="main-content-bg shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-6">
                    <div class="flex items-center">
                        <h2 class="text-lg font-semibold text-gray-900">@yield('title', 'Dashboard')</h2>
                    </div>
                    
                    @auth
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('help.index') }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Help
                        </a>
                        
                        <div class="text-sm text-gray-700">
                            <span class="font-medium">{{ auth()->user()->name }}</span>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('profile.show') }}" class="text-sm text-gray-600 hover:text-gray-900">
                                Perfil
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
                    @else
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            Entrar
                        </a>
                        <a href="{{ route('register') }}" class="btn-teal text-sm">
                            Registrar
                        </a>
                    </div>
                    @endauth
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
                if (window.innerWidth < 1024) {
                    sidebar.classList.toggle('-translate-x-full');
                    overlay.classList.toggle('hidden');
                }
            }

            function closeSidebar() {
                if (window.innerWidth < 1024) {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                }
            }

            function openSidebar() {
                if (window.innerWidth < 1024) {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('hidden');
                }
            }

            mobileMenuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                if (sidebar.classList.contains('-translate-x-full')) {
                    openSidebar();
                } else {
                    closeSidebar();
                }
            });
            
            overlay.addEventListener('click', closeSidebar);

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth < 1024) {
                    if (!sidebar.contains(event.target) && !mobileMenuButton.contains(event.target)) {
                        closeSidebar();
                    }
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    // Desktop: ensure sidebar is visible
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.add('hidden');
                } else {
                    // Mobile: ensure sidebar is hidden by default
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                }
            });

            // Initialize sidebar state
            if (window.innerWidth < 1024) {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>