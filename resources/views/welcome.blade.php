<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QRFlux - Crie QR Codes Profissionais</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900" rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
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
        .pulse-slow {
            animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="font-sans antialiased bg-white">
    <!-- Navigation -->
    <nav class="bg-white/95 backdrop-blur-sm shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
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
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('features') }}" class="text-gray-600 hover:text-gray-900 font-medium transition-colors">
                        Recursos
                    </a>
                    <a href="{{ route('pricing') }}" class="text-gray-600 hover:text-gray-900 font-medium transition-colors">
                        Preços
                    </a>
                    @guest
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 font-medium transition-colors">
                            Entrar
                        </a>
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-2 rounded-lg font-medium hover:from-primary-700 hover:to-primary-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                            Começar Grátis
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-2 rounded-lg font-medium hover:from-primary-700 hover:to-primary-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                            Dashboard
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-primary-50 via-white to-primary-50">
        <!-- Background Pattern -->
        <div class="absolute inset-0 hero-pattern opacity-5"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Column - Content -->
                <div class="text-center lg:text-left">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-primary-100 text-primary-700 text-sm font-medium mb-6">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Plataforma #1 em QR Codes
                    </div>
                    
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight">
                        Crie QR Codes
                        <span class="bg-gradient-to-r from-primary-600 to-primary-700 bg-clip-text text-transparent">
                            Profissionais
                        </span>
                        <br>em Segundos
                    </h1>
                    
                    <p class="mt-6 text-xl text-gray-600 leading-relaxed max-w-2xl">
                        A plataforma mais completa para criação, personalização e rastreamento de QR Codes. 
                        <span class="font-semibold text-gray-900">Ideal para empresas, eventos e uso pessoal.</span>
                    </p>
                    
                    <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        @guest
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-800 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Começar Grátis Agora
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-800 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Ir para Dashboard
                            </a>
                        @endguest
                        
                        <a href="{{ route('features') }}" class="inline-flex items-center justify-center px-8 py-4 border-2 border-primary-200 text-primary-700 font-semibold rounded-xl hover:border-primary-300 hover:bg-primary-50 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-5-8V6a2 2 0 012-2h2a2 2 0 012 2v2"></path>
                            </svg>
                            Ver Recursos
                        </a>
                    </div>
                    
                    <!-- Trust Indicators -->
                    <div class="mt-12 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-8">
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="font-semibold">7 dias grátis</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="font-semibold">Sem cartão de crédito</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="font-semibold">Cancelamento livre</span>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column - Visual -->
                <div class="relative">
                    <div class="relative z-10">
                        <!-- Main QR Code Display -->
                        <div class="bg-white rounded-2xl shadow-2xl p-8 transform rotate-3 hover:rotate-0 transition-transform duration-300">
                            <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl p-6">
                                <!-- QR Code Pattern -->
                                <div class="bg-white rounded-lg p-4 mb-4 shadow-inner relative">
                                    <!-- QR Code using Google Charts API -->
                                    <div class="w-48 h-48 mx-auto flex items-center justify-center">
                                        <img src="https://chart.googleapis.com/chart?chs=192x192&chld=L|0&cht=qr&chl=https://www.fluxti.com.br" 
                                             alt="QR Code para www.fluxti.com.br" 
                                             class="w-full h-full object-contain"
                                             style="display: block;"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <!-- Fallback QR Code - Only shows if image fails to load -->
                                        <div class="hidden w-full h-full bg-gray-100 flex items-center justify-center text-center">
                                            <div class="text-center">
                                                <div class="w-24 h-24 mx-auto mb-4 bg-gray-200 rounded-lg flex items-center justify-center">
                                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                    </svg>
                                                </div>
                                                <p class="text-sm font-medium text-gray-700 mb-1">QR Code</p>
                                                <p class="text-xs text-gray-600">www.fluxti.com.br</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Scan line animation -->
                                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                        <div class="w-full h-0.5 bg-primary-500 opacity-60 animate-pulse"></div>
                                    </div>
                                </div>
                                
                                <!-- QR Code Info -->
                                <div class="text-center">
                                    <div class="flex items-center justify-center mb-2">
                                        <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <p class="text-sm font-medium text-primary-700">QR Code Gerado</p>
                                    </div>
                                    <p class="text-xs text-primary-600 mb-2">www.fluxti.com.br</p>
                                    <div class="flex items-center justify-center text-xs text-gray-500">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        <span>127 scans • 2 min ago</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Floating Elements -->
                        <div class="absolute -top-4 -right-4 w-16 h-16 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full flex items-center justify-center floating">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        
                        <div class="absolute -bottom-4 -left-4 w-12 h-12 bg-gradient-to-r from-green-400 to-blue-500 rounded-full flex items-center justify-center floating" style="animation-delay: 1s;">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Background Decoration -->
                    <div class="absolute inset-0 bg-gradient-to-r from-primary-200 to-primary-300 rounded-2xl transform rotate-6 scale-105 -z-10"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                    Por que escolher nossa plataforma?
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Tudo que você precisa para criar, personalizar e rastrear QR Codes profissionais
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-primary-200">
                    <div class="w-12 h-12 bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">20+ Tipos de QR Code</h3>
                    <p class="text-gray-600 leading-relaxed">
                        URL, vCard, Wi-Fi, E-mail, SMS, Texto, Localização e muito mais. 
                        Suporte completo a todos os tipos de QR Code.
                    </p>
                </div>
                
                <!-- Feature 2 -->
                <div class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-primary-200">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Personalização Total</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Cores personalizadas, logos, stickers e múltiplos formatos. 
                        Crie QR Codes únicos que representam sua marca.
                    </p>
                </div>
                
                <!-- Feature 3 -->
                <div class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-primary-200">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Estatísticas Avançadas</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Rastreie scans, localização, dispositivos e horários. 
                        Relatórios detalhados para análise completa.
                    </p>
                </div>
                
                <!-- Feature 4 -->
                <div class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-primary-200">
                    <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">QR Codes Dinâmicos</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Edite o conteúdo sem alterar o QR Code físico. 
                        Perfeito para campanhas e atualizações em tempo real.
                    </p>
                </div>
                
                <!-- Feature 5 -->
                <div class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-primary-200">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Segurança Total</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Dados protegidos com criptografia de ponta. 
                        Conformidade com LGPD e padrões internacionais.
                    </p>
                </div>
                
                <!-- Feature 6 -->
                <div class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-primary-200">
                    <div class="w-12 h-12 bg-gradient-to-r from-pink-500 to-pink-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Fácil de Usar</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Interface intuitiva e amigável. 
                        Crie QR Codes profissionais em poucos cliques.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-20 bg-gradient-to-r from-primary-600 to-primary-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">
                    Números que impressionam
                </h2>
                <p class="text-xl text-primary-100">
                    Milhares de usuários já confiam em nossa plataforma
                </p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl sm:text-5xl font-bold text-white mb-2">10K+</div>
                    <div class="text-primary-100">QR Codes Criados</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl sm:text-5xl font-bold text-white mb-2">50K+</div>
                    <div class="text-primary-100">Scans Rastreados</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl sm:text-5xl font-bold text-white mb-2">99.9%</div>
                    <div class="text-primary-100">Uptime</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl sm:text-5xl font-bold text-white mb-2">24/7</div>
                    <div class="text-primary-100">Suporte</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-white">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-6">
                Pronto para começar sua jornada?
            </h2>
            <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                Junte-se a milhares de usuários que já estão criando QR Codes profissionais 
                com nossa plataforma. Comece grátis hoje mesmo!
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                @guest
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-800 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Criar Conta Grátis
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-800 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Ir para Dashboard
                    </a>
                @endguest
                
                <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>7 dias grátis • Sem cartão • Cancelamento livre</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-gradient-to-r from-primary-600 to-primary-700 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold">QRFlux</h3>
                    </div>
                    <p class="text-gray-400 mb-4 max-w-md">
                        A plataforma mais completa para criação, personalização e rastreamento de QR Codes profissionais.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Produto</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Recursos</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Preços</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">API</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Integrações</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Suporte</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Central de Ajuda</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contato</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Status</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Comunidade</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p class="text-gray-400">
                    &copy; {{ date('Y') }} QRFlux. Todos os direitos reservados.
                </p>
            </div>
        </div>
    </footer>

    <!-- Smooth scrolling script -->
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Ensure QR Code loads properly
        document.addEventListener('DOMContentLoaded', function() {
            const qrImg = document.querySelector('img[alt="QR Code para www.fluxti.com.br"]');
            if (qrImg) {
                qrImg.onload = function() {
                    console.log('QR Code carregado com sucesso');
                };
                qrImg.onerror = function() {
                    console.log('Erro ao carregar QR Code, mostrando fallback');
                };
            }
        });

    </script>
</body>
</html>