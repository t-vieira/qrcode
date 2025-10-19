<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recursos - QRFlux</title>
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
</head>
<body class="font-sans antialiased bg-white">
    <!-- Navigation -->
    <nav class="bg-white/95 backdrop-blur-sm shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <div class="w-8 h-8 bg-gradient-to-r from-primary-600 to-primary-700 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <h1 class="text-xl font-bold bg-gradient-to-r from-primary-600 to-primary-700 bg-clip-text text-transparent">
                            QRFlux
                        </h1>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900 font-medium transition-colors">
                        Início
                    </a>
                    <a href="#features" class="text-primary-600 font-medium">
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
    <section class="py-20 bg-gradient-to-br from-primary-50 via-white to-primary-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 mb-6">
                Recursos que fazem a
                <span class="bg-gradient-to-r from-primary-600 to-primary-700 bg-clip-text text-transparent">
                    diferença
                </span>
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-12">
                Descubra todas as funcionalidades que tornam nossa plataforma a melhor escolha para criar QR Codes profissionais.
            </p>
        </div>
    </section>

    <!-- Main Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- QR Code Types -->
            <div class="mb-20">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                        20+ Tipos de QR Code
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Crie QR Codes para qualquer necessidade com nossa ampla variedade de tipos
                    </p>
                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">URL</h3>
                        <p class="text-gray-600">Direcione usuários para qualquer site ou página web com QR Codes de URL.</p>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">vCard</h3>
                        <p class="text-gray-600">Compartilhe informações de contato facilmente com QR Codes de cartão de visita digital.</p>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">E-mail</h3>
                        <p class="text-gray-600">Crie QR Codes que abrem automaticamente o cliente de e-mail com destinatário e assunto.</p>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                        <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">SMS</h3>
                        <p class="text-gray-600">Gere QR Codes que abrem o aplicativo de mensagens com número e texto pré-definidos.</p>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                        <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-red-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Wi-Fi</h3>
                        <p class="text-gray-600">Facilite a conexão à rede Wi-Fi com QR Codes que conectam automaticamente os dispositivos.</p>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
                        <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Localização</h3>
                        <p class="text-gray-600">Compartilhe coordenadas GPS e endereços com QR Codes de localização.</p>
                    </div>
                </div>
            </div>

            <!-- Customization Features -->
            <div class="mb-20">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                        Personalização Total
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Crie QR Codes únicos que representam sua marca com nossa ferramenta de personalização
                    </p>
                </div>
                
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <div class="space-y-8">
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-gradient-to-r from-pink-500 to-pink-600 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">Cores Personalizadas</h3>
                                    <p class="text-gray-600">Escolha as cores do QR Code para combinar com sua identidade visual.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">Logos e Stickers</h3>
                                    <p class="text-gray-600">Adicione seu logo ou stickers personalizados ao centro do QR Code.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">Múltiplos Formatos</h3>
                                    <p class="text-gray-600">Exporte em PNG, JPG, SVG e PDF com diferentes resoluções.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-2xl p-8">
                        <div class="bg-white rounded-xl p-6 shadow-lg">
                            <div class="text-center mb-6">
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Preview do QR Code</h4>
                                <div class="w-32 h-32 mx-auto bg-gray-100 rounded-lg flex items-center justify-center">
                                    <div class="w-24 h-24 bg-black grid grid-cols-4 gap-1 p-2">
                                        <div class="bg-white"></div><div class="bg-black"></div><div class="bg-white"></div><div class="bg-black"></div>
                                        <div class="bg-black"></div><div class="bg-white"></div><div class="bg-black"></div><div class="bg-white"></div>
                                        <div class="bg-white"></div><div class="bg-black"></div><div class="bg-white"></div><div class="bg-black"></div>
                                        <div class="bg-black"></div><div class="bg-white"></div><div class="bg-black"></div><div class="bg-white"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Cor do QR Code:</span>
                                    <div class="w-6 h-6 bg-primary-600 rounded"></div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Fundo:</span>
                                    <div class="w-6 h-6 bg-white border border-gray-300 rounded"></div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Logo:</span>
                                    <span class="text-sm text-gray-500">Adicionado</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Features -->
            <div class="mb-20">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                        Estatísticas Avançadas
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Acompanhe o desempenho dos seus QR Codes com relatórios detalhados e insights valiosos
                    </p>
                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 text-center">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Total de Scans</h3>
                        <p class="text-3xl font-bold text-primary-600 mb-2">1,247</p>
                        <p class="text-sm text-gray-600">+12% este mês</p>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 text-center">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Localização</h3>
                        <p class="text-3xl font-bold text-primary-600 mb-2">23</p>
                        <p class="text-sm text-gray-600">países diferentes</p>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 text-center">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Dispositivos</h3>
                        <p class="text-3xl font-bold text-primary-600 mb-2">847</p>
                        <p class="text-sm text-gray-600">únicos este mês</p>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 text-center">
                        <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Horários</h3>
                        <p class="text-3xl font-bold text-primary-600 mb-2">24h</p>
                        <p class="text-sm text-gray-600">monitoramento</p>
                    </div>
                </div>
            </div>

            <!-- Dynamic QR Codes -->
            <div class="mb-20">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                        QR Codes Dinâmicos
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Edite o conteúdo dos seus QR Codes sem alterar o código físico. Perfeito para campanhas e atualizações.
                    </p>
                </div>
                
                <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-2xl p-8 text-white">
                    <div class="grid lg:grid-cols-2 gap-12 items-center">
                        <div>
                            <h3 class="text-2xl font-bold mb-4">Mude o conteúdo, mantenha o QR Code</h3>
                            <p class="text-primary-100 mb-6">
                                Com QR Codes dinâmicos, você pode alterar o destino do seu QR Code a qualquer momento, 
                                sem precisar reimprimir ou redistribuir o código físico.
                            </p>
                            <ul class="space-y-3">
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>Atualizações em tempo real</span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>Histórico de alterações</span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>Redirecionamentos inteligentes</span>
                                </li>
                            </ul>
                        </div>
                        <div class="bg-white rounded-xl p-6">
                            <div class="text-center mb-4">
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Exemplo de Uso</h4>
                                <p class="text-sm text-gray-600">QR Code para campanha promocional</p>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm text-gray-700">Status:</span>
                                    <span class="text-sm text-green-600 font-medium">Ativo</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm text-gray-700">Destino atual:</span>
                                    <span class="text-sm text-gray-900">promocao.com.br</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm text-gray-700">Scans hoje:</span>
                                    <span class="text-sm text-gray-900">47</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-primary-600">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">
                Pronto para experimentar?
            </h2>
            <p class="text-xl text-primary-100 mb-8">
                Comece seu teste gratuito de 7 dias e descubra todas essas funcionalidades.
            </p>
            @guest
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-primary-600 font-semibold rounded-xl hover:bg-primary-50 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Começar Teste Grátis
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-primary-600 font-semibold rounded-xl hover:bg-primary-50 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Ir para Dashboard
                </a>
            @endguest
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
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Produto</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-colors">Início</a></li>
                        <li><a href="#features" class="text-gray-400 hover:text-white transition-colors">Recursos</a></li>
                        <li><a href="{{ route('pricing') }}" class="text-gray-400 hover:text-white transition-colors">Preços</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Suporte</h4>
                    <ul class="space-y-2">
                        <li><a href="mailto:suporte@qrcodesaas.com" class="text-gray-400 hover:text-white transition-colors">Contato</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Central de Ajuda</a></li>
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
</body>
</html>
