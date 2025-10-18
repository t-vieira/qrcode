@extends('layouts.app')

@section('title', 'Termos de Uso')

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
                    <h1 class="text-3xl font-bold text-gray-900">Termos de Uso</h1>
                    <p class="mt-2 text-gray-600">Última atualização: {{ now()->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Conteúdo -->
        <div class="prose max-w-none">
            <div class="bg-white shadow rounded-lg p-8">
                <h2>1. Aceitação dos Termos</h2>
                <p>Ao utilizar o QR Code SaaS, você concorda em cumprir estes Termos de Uso. Se não concordar com qualquer parte destes termos, não utilize nosso serviço.</p>

                <h2>2. Descrição do Serviço</h2>
                <p>O QR Code SaaS é uma plataforma que permite:</p>
                <ul>
                    <li>Criação de QR Codes estáticos e dinâmicos</li>
                    <li>Personalização visual dos QR Codes</li>
                    <li>Rastreamento de estatísticas de acesso</li>
                    <li>Gerenciamento de equipes</li>
                    <li>Domínios customizados</li>
                    <li>Exportação de relatórios</li>
                </ul>

                <h2>3. Conta de Usuário</h2>
                <h3>3.1 Registro</h3>
                <ul>
                    <li>Você deve fornecer informações verdadeiras e precisas</li>
                    <li>É responsável por manter a confidencialidade de sua conta</li>
                    <li>Deve notificar-nos imediatamente sobre uso não autorizado</li>
                    <li>Deve ter pelo menos 18 anos de idade</li>
                </ul>

                <h3>3.2 Período de Teste</h3>
                <ul>
                    <li>7 dias grátis para novos usuários</li>
                    <li>Acesso a todas as funcionalidades durante o teste</li>
                    <li>Cancelamento automático se não houver pagamento</li>
                </ul>

                <h2>4. Pagamentos e Assinaturas</h2>
                <h3>4.1 Cobrança</h3>
                <ul>
                    <li>Pagamento mensal recorrente</li>
                    <li>Processamento seguro via Mercado Pago</li>
                    <li>Cobrança automática até cancelamento</li>
                </ul>

                <h3>4.2 Cancelamento</h3>
                <ul>
                    <li>Cancelamento a qualquer momento</li>
                    <li>Efeito imediato do cancelamento</li>
                    <li>Sem reembolso de períodos já pagos</li>
                    <li>QR Codes estáticos continuam funcionando</li>
                </ul>

                <h2>5. Uso Aceitável</h2>
                <p>Você concorda em NÃO:</p>
                <ul>
                    <li>Usar o serviço para atividades ilegais</li>
                    <li>Criar QR Codes com conteúdo ofensivo ou prejudicial</li>
                    <li>Tentar acessar contas de outros usuários</li>
                    <li>Interferir no funcionamento do serviço</li>
                    <li>Usar bots ou scripts automatizados</li>
                    <li>Violar direitos de propriedade intelectual</li>
                </ul>

                <h2>6. Propriedade Intelectual</h2>
                <h3>6.1 Seu Conteúdo</h3>
                <ul>
                    <li>Você mantém a propriedade de seus QR Codes</li>
                    <li>Concede-nos licença para hospedar e exibir</li>
                    <li>É responsável pelo conteúdo que cria</li>
                </ul>

                <h3>6.2 Nosso Serviço</h3>
                <ul>
                    <li>Mantemos direitos sobre a plataforma</li>
                    <li>Você não pode copiar ou modificar o software</li>
                    <li>Respeitamos marcas registradas de terceiros</li>
                </ul>

                <h2>7. Limitação de Responsabilidade</h2>
                <p>O QR Code SaaS não se responsabiliza por:</p>
                <ul>
                    <li>Perda de dados ou interrupção do serviço</li>
                    <li>Danos indiretos ou consequenciais</li>
                    <li>Conteúdo de sites externos linkados</li>
                    <li>Falhas de terceiros (Mercado Pago, WhatsApp)</li>
                </ul>

                <h2>8. Disponibilidade do Serviço</h2>
                <ul>
                    <li>Esforçamo-nos para manter 99% de uptime</li>
                    <li>Podem ocorrer manutenções programadas</li>
                    <li>Não garantimos disponibilidade 100%</li>
                    <li>Notificaremos sobre interrupções prolongadas</li>
                </ul>

                <h2>9. Privacidade e Proteção de Dados</h2>
                <ul>
                    <li>Coletamos dados conforme nossa Política de Privacidade</li>
                    <li>Cumprimos a LGPD brasileira</li>
                    <li>Você pode solicitar exclusão de seus dados</li>
                    <li>Dados são protegidos com criptografia</li>
                </ul>

                <h2>10. Modificações dos Termos</h2>
                <ul>
                    <li>Podemos alterar estes termos a qualquer momento</li>
                    <li>Notificaremos sobre mudanças significativas</li>
                    <li>Uso continuado implica aceitação</li>
                    <li>Versão atual sempre disponível no site</li>
                </ul>

                <h2>11. Rescisão</h2>
                <h3>11.1 Por Você</h3>
                <ul>
                    <li>Cancelamento a qualquer momento</li>
                    <li>Efeito imediato</li>
                    <li>Dados mantidos por 30 dias</li>
                </ul>

                <h3>11.2 Por Nós</h3>
                <ul>
                    <li>Em caso de violação dos termos</li>
                    <li>Com aviso prévio de 30 dias</li>
                    <li>Em caso de atividade ilegal</li>
                </ul>

                <h2>12. Lei Aplicável</h2>
                <p>Estes termos são regidos pelas leis brasileiras. Disputas serão resolvidas nos tribunais competentes do Brasil.</p>

                <h2>13. Contato</h2>
                <p>Para questões sobre estes termos:</p>
                <ul>
                    <li>Email: legal@qrcodesaas.com</li>
                    <li>WhatsApp: (11) 99999-9999</li>
                    <li>Endereço: [Endereço da empresa]</li>
                </ul>

                <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-yellow-800 text-sm">
                        <strong>Aviso Legal:</strong> Estes termos constituem um acordo legal entre você e o QR Code SaaS. Leia cuidadosamente antes de aceitar.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
