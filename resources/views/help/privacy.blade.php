@extends('layouts.app')

@section('title', 'Política de Privacidade')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <a href="{{ route('help.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Política de Privacidade</h1>
                    <p class="mt-2 text-gray-600">Última atualização: {{ now()->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Conteúdo -->
        <div class="prose max-w-none">
            <div class="bg-white shadow rounded-lg p-8">
                <h2>1. Informações Gerais</h2>
                <p>Esta Política de Privacidade descreve como o QR Code SaaS ("nós", "nosso" ou "a empresa") coleta, usa e protege suas informações quando você utiliza nosso serviço.</p>

                <h2>2. Informações que Coletamos</h2>
                <h3>2.1 Informações Pessoais</h3>
                <ul>
                    <li>Nome completo</li>
                    <li>Endereço de email</li>
                    <li>Número de telefone (opcional)</li>
                    <li>Informações de pagamento (processadas pelo Mercado Pago)</li>
                </ul>

                <h3>2.2 Informações de Uso</h3>
                <ul>
                    <li>QR Codes criados e seu conteúdo</li>
                    <li>Estatísticas de acesso aos QR Codes</li>
                    <li>Dados de localização (país/cidade) dos scans</li>
                    <li>Tipo de dispositivo e navegador utilizado</li>
                    <li>Data e hora dos acessos</li>
                </ul>

                <h3>2.3 Informações Técnicas</h3>
                <ul>
                    <li>Endereço IP (anonimizado após 30 dias)</li>
                    <li>User-Agent do navegador</li>
                    <li>Cookies e tecnologias similares</li>
                </ul>

                <h2>3. Como Usamos suas Informações</h2>
                <p>Utilizamos suas informações para:</p>
                <ul>
                    <li>Fornecer e melhorar nossos serviços</li>
                    <li>Processar pagamentos e gerenciar assinaturas</li>
                    <li>Enviar notificações importantes sobre sua conta</li>
                    <li>Fornecer suporte ao cliente</li>
                    <li>Gerar estatísticas e relatórios</li>
                    <li>Cumprir obrigações legais</li>
                </ul>

                <h2>4. Compartilhamento de Informações</h2>
                <p>Não vendemos, alugamos ou compartilhamos suas informações pessoais com terceiros, exceto:</p>
                <ul>
                    <li>Com provedores de serviços (Mercado Pago, WhatsApp Business API)</li>
                    <li>Quando exigido por lei</li>
                    <li>Para proteger nossos direitos legais</li>
                    <li>Com seu consentimento explícito</li>
                </ul>

                <h2>5. Segurança dos Dados</h2>
                <p>Implementamos medidas de segurança técnicas e organizacionais para proteger suas informações:</p>
                <ul>
                    <li>Criptografia SSL/TLS para transmissão de dados</li>
                    <li>Armazenamento seguro em servidores protegidos</li>
                    <li>Acesso restrito aos dados pessoais</li>
                    <li>Monitoramento contínuo de segurança</li>
                </ul>

                <h2>6. Retenção de Dados</h2>
                <p>Mantemos suas informações pelo tempo necessário para:</p>
                <ul>
                    <li>Fornecer nossos serviços</li>
                    <li>Cumprir obrigações legais</li>
                    <li>Resolver disputas</li>
                    <li>Fazer cumprir nossos acordos</li>
                </ul>
                <p>Dados de scans são anonimizados após 1 ano. QR Codes estáticos são mantidos indefinidamente.</p>

                <h2>7. Seus Direitos (LGPD)</h2>
                <p>Você tem o direito de:</p>
                <ul>
                    <li>Acessar suas informações pessoais</li>
                    <li>Corrigir dados incorretos</li>
                    <li>Solicitar a exclusão de seus dados</li>
                    <li>Portabilidade dos dados</li>
                    <li>Revogar consentimento</li>
                    <li>Opor-se ao tratamento de dados</li>
                </ul>

                <h2>8. Cookies e Tecnologias Similares</h2>
                <p>Utilizamos cookies para:</p>
                <ul>
                    <li>Manter sua sessão ativa</li>
                    <li>Lembrar suas preferências</li>
                    <li>Analisar o uso do serviço</li>
                    <li>Melhorar a experiência do usuário</li>
                </ul>

                <h2>9. Menores de Idade</h2>
                <p>Nosso serviço não é direcionado a menores de 18 anos. Não coletamos intencionalmente informações de menores.</p>

                <h2>10. Alterações na Política</h2>
                <p>Podemos atualizar esta política periodicamente. Notificaremos sobre mudanças significativas por email ou através do serviço.</p>

                <h2>11. Contato</h2>
                <p>Para questões sobre privacidade, entre em contato:</p>
                <ul>
                    <li>Email: privacidade@qrcodesaas.com</li>
                    <li>WhatsApp: (11) 99999-9999</li>
                    <li>Endereço: [Endereço da empresa]</li>
                </ul>

                <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-blue-800 text-sm">
                        <strong>Importante:</strong> Esta política está em conformidade com a Lei Geral de Proteção de Dados (LGPD) do Brasil.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
