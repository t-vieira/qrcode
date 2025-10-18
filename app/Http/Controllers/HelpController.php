<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller
{
    /**
     * Página principal de ajuda
     */
    public function index()
    {
        return view('help.index');
    }

    /**
     * FAQ - Perguntas Frequentes
     */
    public function faq()
    {
        $faqs = [
            'conta' => [
                'title' => 'Conta e Cadastro',
                'items' => [
                    [
                        'question' => 'Como criar uma conta?',
                        'answer' => 'Para criar uma conta, clique em "Cadastrar" no menu superior e preencha o formulário com seus dados. Você receberá um email de verificação que deve ser confirmado para ativar sua conta.'
                    ],
                    [
                        'question' => 'Posso usar o sistema gratuitamente?',
                        'answer' => 'Sim! Oferecemos 7 dias grátis para novos usuários. Durante este período, você pode criar QR Codes ilimitados e acessar todas as funcionalidades básicas.'
                    ],
                    [
                        'question' => 'Como funciona o período de teste?',
                        'answer' => 'O período de teste dura 7 dias a partir do cadastro. Durante este tempo, você tem acesso a todas as funcionalidades. Após a expiração, QR Codes dinâmicos e estatísticas avançadas ficam limitados.'
                    ],
                    [
                        'question' => 'Posso cancelar minha assinatura a qualquer momento?',
                        'answer' => 'Sim, você pode cancelar sua assinatura a qualquer momento através do painel de controle. O cancelamento é imediato e você não será cobrado no próximo ciclo.'
                    ]
                ]
            ],
            'pagamento' => [
                'title' => 'Pagamentos e Assinaturas',
                'items' => [
                    [
                        'question' => 'Quais formas de pagamento são aceitas?',
                        'answer' => 'Aceitamos cartão de crédito (Visa, Mastercard, Elo) e PIX. Todos os pagamentos são processados de forma segura pelo Mercado Pago.'
                    ],
                    [
                        'question' => 'Quando serei cobrado?',
                        'answer' => 'A cobrança é mensal e automática. O primeiro pagamento é processado após o período de teste de 7 dias. Você receberá um email de confirmação antes da cobrança.'
                    ],
                    [
                        'question' => 'Posso alterar meu método de pagamento?',
                        'answer' => 'Sim, você pode alterar seu método de pagamento a qualquer momento através do painel de assinatura. As alterações são aplicadas no próximo ciclo de cobrança.'
                    ],
                    [
                        'question' => 'O que acontece se meu pagamento for recusado?',
                        'answer' => 'Se o pagamento for recusado, você receberá um email de notificação. Tentaremos processar novamente em 3 dias. Se não conseguir, sua assinatura será suspensa temporariamente.'
                    ]
                ]
            ],
            'qrcodes' => [
                'title' => 'QR Codes',
                'items' => [
                    [
                        'question' => 'Quantos QR Codes posso criar?',
                        'answer' => 'Durante o período de teste e com assinatura ativa, você pode criar QR Codes ilimitados. Não há limite de quantidade.'
                    ],
                    [
                        'question' => 'Qual a diferença entre QR Code estático e dinâmico?',
                        'answer' => 'QR Codes estáticos contêm informações fixas (URL, texto, etc.) e não podem ser editados após a criação. QR Codes dinâmicos podem ter seu conteúdo alterado a qualquer momento e incluem estatísticas de acesso.'
                    ],
                    [
                        'question' => 'Posso personalizar a aparência dos QR Codes?',
                        'answer' => 'Sim! Você pode personalizar cores, adicionar logo, escolher formato (quadrado/arredondado), resolução e formato de exportação (PNG, JPG, SVG, EPS).'
                    ],
                    [
                        'question' => 'Os QR Codes expiram?',
                        'answer' => 'Não, os QR Codes não expiram. Eles continuam funcionando indefinidamente, mesmo após o cancelamento da assinatura (apenas QR Codes estáticos).'
                    ]
                ]
            ],
            'estatisticas' => [
                'title' => 'Estatísticas e Rastreamento',
                'items' => [
                    [
                        'question' => 'Que informações são coletadas dos scans?',
                        'answer' => 'Coletamos dados anônimos como localização (país/cidade), tipo de dispositivo, navegador, data/hora do scan e se é um scan único (primeiro scan do IP/device).'
                    ],
                    [
                        'question' => 'Posso exportar minhas estatísticas?',
                        'answer' => 'Sim, você pode exportar relatórios em formato CSV com todos os dados de scans, incluindo localização, dispositivo e horários.'
                    ],
                    [
                        'question' => 'As estatísticas são em tempo real?',
                        'answer' => 'As estatísticas são atualizadas em tempo real. Você pode ver o número de scans e dados detalhados imediatamente após cada scan.'
                    ],
                    [
                        'question' => 'Posso ver quem escaneou meus QR Codes?',
                        'answer' => 'Não coletamos dados pessoais dos usuários que escaneiam. Apenas dados anônimos de localização e dispositivo para fins estatísticos.'
                    ]
                ]
            ],
            'tecnico' => [
                'title' => 'Aspectos Técnicos',
                'items' => [
                    [
                        'question' => 'Qual a resolução máxima dos QR Codes?',
                        'answer' => 'Você pode gerar QR Codes com resolução de 100x100 até 2000x2000 pixels. Para impressão, recomendamos pelo menos 300x300 pixels.'
                    ],
                    [
                        'question' => 'Posso usar meu próprio domínio?',
                        'answer' => 'Sim! Usuários premium podem configurar domínios customizados para suas URLs curtas. Isso é útil para branding e confiança.'
                    ],
                    [
                        'question' => 'Os QR Codes funcionam offline?',
                        'answer' => 'QR Codes estáticos funcionam offline (apenas mostram o conteúdo). QR Codes dinâmicos precisam de conexão com a internet para redirecionar.'
                    ],
                    [
                        'question' => 'Posso integrar com outras ferramentas?',
                        'answer' => 'Sim, oferecemos API para integração com outras ferramentas. Entre em contato conosco para mais informações sobre integrações.'
                    ]
                ]
            ]
        ];

        return view('help.faq', compact('faqs'));
    }

    /**
     * Tutoriais
     */
    public function tutorials()
    {
        $tutorials = [
            [
                'title' => 'Como criar seu primeiro QR Code',
                'description' => 'Aprenda a criar QR Codes passo a passo',
                'duration' => '5 min',
                'difficulty' => 'Iniciante',
                'category' => 'Básico',
                'steps' => [
                    'Acesse o dashboard e clique em "Criar QR Code"',
                    'Escolha o tipo de QR Code (URL, texto, vCard, etc.)',
                    'Preencha as informações necessárias',
                    'Personalize a aparência (cores, logo, formato)',
                    'Clique em "Gerar QR Code" e baixe o arquivo'
                ]
            ],
            [
                'title' => 'Configurando QR Codes dinâmicos',
                'description' => 'Entenda a diferença e como usar QR Codes dinâmicos',
                'duration' => '8 min',
                'difficulty' => 'Intermediário',
                'category' => 'Avançado',
                'steps' => [
                    'Crie um QR Code dinâmico selecionando a opção',
                    'Configure o conteúdo inicial (URL, texto, etc.)',
                    'Teste o QR Code para garantir que funciona',
                    'Edite o conteúdo posteriormente sem alterar o QR Code físico',
                    'Acompanhe as estatísticas de acesso em tempo real'
                ]
            ],
            [
                'title' => 'Personalizando a aparência dos QR Codes',
                'description' => 'Dicas para criar QR Codes únicos e atrativos',
                'duration' => '10 min',
                'difficulty' => 'Intermediário',
                'category' => 'Design',
                'steps' => [
                    'Escolha cores que combinem com sua marca',
                    'Adicione um logo central (máximo 30% do QR Code)',
                    'Selecione o formato (quadrado ou arredondado)',
                    'Ajuste a resolução conforme a necessidade',
                    'Teste a legibilidade antes de imprimir'
                ]
            ],
            [
                'title' => 'Configurando domínio customizado',
                'description' => 'Como usar seu próprio domínio para URLs curtas',
                'duration' => '15 min',
                'difficulty' => 'Avançado',
                'category' => 'Premium',
                'steps' => [
                    'Acesse as configurações de domínio customizado',
                    'Adicione seu domínio (ex: qr.minhaempresa.com)',
                    'Configure o registro DNS conforme instruções',
                    'Aguarde a verificação automática',
                    'Defina como domínio primário e comece a usar'
                ]
            ],
            [
                'title' => 'Analisando estatísticas e relatórios',
                'description' => 'Como interpretar os dados de acesso dos seus QR Codes',
                'duration' => '7 min',
                'difficulty' => 'Iniciante',
                'category' => 'Analytics',
                'steps' => [
                    'Acesse as estatísticas do QR Code desejado',
                    'Visualize o gráfico de acessos por período',
                    'Analise a localização dos usuários',
                    'Verifique os tipos de dispositivos utilizados',
                    'Exporte relatórios em CSV para análise detalhada'
                ]
            ],
            [
                'title' => 'Criando equipes e compartilhando QR Codes',
                'description' => 'Como trabalhar em equipe e compartilhar QR Codes',
                'duration' => '12 min',
                'difficulty' => 'Intermediário',
                'category' => 'Colaboração',
                'steps' => [
                    'Crie uma equipe e convide membros',
                    'Configure permissões para cada membro',
                    'Compartilhe QR Codes com a equipe',
                    'Organize QR Codes em pastas compartilhadas',
                    'Monitore a atividade da equipe'
                ]
            ]
        ];

        return view('help.tutorials', compact('tutorials'));
    }

    /**
     * Política de Privacidade
     */
    public function privacy()
    {
        return view('help.privacy');
    }

    /**
     * Termos de Uso
     */
    public function terms()
    {
        return view('help.terms');
    }

    /**
     * Contato
     */
    public function contact()
    {
        return view('help.contact');
    }
}
