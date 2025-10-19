<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preços - QRFlux</title>
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
<body class="font-sans antialiased bg-gray-50">
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
                    <a href="#features" class="text-gray-600 hover:text-gray-900 font-medium transition-colors">
                        Recursos
                    </a>
                    <a href="#pricing" class="text-primary-600 font-medium">
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
                Planos que cabem no seu
                <span class="bg-gradient-to-r from-primary-600 to-primary-700 bg-clip-text text-transparent">
                    bolso
                </span>
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-12">
                Escolha o plano ideal para suas necessidades. Todos os planos incluem teste gratuito de 7 dias.
            </p>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <!-- Free Plan -->
                <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300">
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Gratuito</h3>
                        <div class="mb-4">
                            <span class="text-4xl font-bold text-gray-900">R$ 0</span>
                            <span class="text-gray-600">/mês</span>
                        </div>
                        <p class="text-gray-600">Perfeito para começar</p>
                    </div>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">5 QR Codes</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Tipos básicos</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Estatísticas básicas</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Suporte por email</span>
                        </li>
                    </ul>
                    
                    @guest
                        <a href="{{ route('register') }}" class="w-full bg-gray-100 text-gray-900 font-semibold py-3 px-6 rounded-xl hover:bg-gray-200 transition-all duration-200 text-center block">
                            Começar Grátis
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="w-full bg-gray-100 text-gray-900 font-semibold py-3 px-6 rounded-xl hover:bg-gray-200 transition-all duration-200 text-center block">
                            Ir para Dashboard
                        </a>
                    @endguest
                </div>

                <!-- Pro Plan - Featured -->
                <div class="bg-gradient-to-br from-primary-600 to-primary-700 rounded-2xl p-8 shadow-2xl transform scale-105 relative">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <span class="bg-yellow-400 text-yellow-900 px-4 py-1 rounded-full text-sm font-semibold">
                            Mais Popular
                        </span>
                    </div>
                    
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold text-white mb-2">Profissional</h3>
                        <div class="mb-4">
                            <span class="text-4xl font-bold text-white">R$ 29,90</span>
                            <span class="text-primary-100">/mês</span>
                        </div>
                        <p class="text-primary-100">Ideal para empresas</p>
                    </div>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-white">QR Codes ilimitados</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-white">Todos os tipos</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-white">Personalização total</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-white">Estatísticas avançadas</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-white">QR Codes dinâmicos</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-white">Suporte prioritário</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-white">API completa</span>
                        </li>
                    </ul>
                    
                    @guest
                        <a href="{{ route('register') }}" class="w-full bg-white text-primary-600 font-semibold py-3 px-6 rounded-xl hover:bg-primary-50 transition-all duration-200 text-center block">
                            Começar Teste Grátis
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="w-full bg-white text-primary-600 font-semibold py-3 px-6 rounded-xl hover:bg-primary-50 transition-all duration-200 text-center block">
                            Ir para Dashboard
                        </a>
                    @endguest
                </div>

                <!-- Enterprise Plan -->
                <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300">
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Empresarial</h3>
                        <div class="mb-4">
                            <span class="text-4xl font-bold text-gray-900">R$ 99,90</span>
                            <span class="text-gray-600">/mês</span>
                        </div>
                        <p class="text-gray-600">Para grandes empresas</p>
                    </div>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Tudo do Profissional</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Múltiplos usuários</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Relatórios personalizados</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Suporte dedicado</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Integração customizada</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">SLA garantido</span>
                        </li>
                    </ul>
                    
                    <a href="mailto:contato@qrcodesaas.com" class="w-full bg-primary-600 text-white font-semibold py-3 px-6 rounded-xl hover:bg-primary-700 transition-all duration-200 text-center block">
                        Falar com Vendas
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                    Perguntas Frequentes
                </h2>
                <p class="text-xl text-gray-600">
                    Tire suas dúvidas sobre nossos planos e funcionalidades
                </p>
            </div>
            
            <div class="space-y-8">
                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Posso cancelar a qualquer momento?</h3>
                    <p class="text-gray-600">Sim! Você pode cancelar sua assinatura a qualquer momento. Não há taxas de cancelamento ou compromissos de longo prazo.</p>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">O que acontece com meus QR Codes se eu cancelar?</h3>
                    <p class="text-gray-600">Seus QR Codes continuam funcionando normalmente. Você terá acesso ao plano gratuito com 5 QR Codes.</p>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Posso mudar de plano a qualquer momento?</h3>
                    <p class="text-gray-600">Sim! Você pode fazer upgrade ou downgrade do seu plano a qualquer momento. As mudanças são aplicadas imediatamente.</p>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Há desconto para pagamento anual?</h3>
                    <p class="text-gray-600">Sim! Oferecemos 20% de desconto para pagamentos anuais em todos os planos.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-primary-600">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">
                Pronto para começar?
            </h2>
            <p class="text-xl text-primary-100 mb-8">
                Comece seu teste gratuito de 7 dias hoje mesmo. Sem cartão de crédito necessário.
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
                        <li><a href="#pricing" class="text-gray-400 hover:text-white transition-colors">Preços</a></li>
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
