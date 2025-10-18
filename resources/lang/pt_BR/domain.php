<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Linhas de Linguagem para Domínios Customizados
    |--------------------------------------------------------------------------
    |
    | As seguintes linhas de linguagem são usadas para o sistema de domínios
    | customizados incluindo configuração, verificação e gerenciamento.
    |
    */

    'title' => 'Domínios Customizados',
    'create' => 'Adicionar Domínio',
    'edit' => 'Editar Domínio',
    'delete' => 'Excluir Domínio',
    'verify' => 'Verificar Domínio',
    'set_primary' => 'Definir como Primário',
    'instructions' => 'Instruções DNS',

    'fields' => [
        'domain' => 'Domínio',
        'status' => 'Status',
        'is_primary' => 'Primário',
        'dns_record' => 'Registro DNS',
        'verified_at' => 'Verificado em',
        'created_at' => 'Criado em',
        'updated_at' => 'Atualizado em',
    ],

    'status' => [
        'pending' => 'Pendente',
        'verified' => 'Verificado',
        'failed' => 'Falhou',
        'expired' => 'Expirado',
    ],

    'types' => [
        'cname' => 'CNAME',
        'txt' => 'TXT',
        'a' => 'A',
        'aaaa' => 'AAAA',
    ],

    'verification' => [
        'title' => 'Verificação de Domínio',
        'description' => 'Para usar seu domínio customizado, você precisa configurar um registro DNS.',
        'current_status' => 'Status Atual',
        'verification_method' => 'Método de Verificação',
        'dns_instructions' => 'Instruções DNS',
        'verify_now' => 'Verificar Agora',
        'check_again' => 'Verificar Novamente',
        'verification_successful' => 'Domínio verificado com sucesso!',
        'verification_failed' => 'Falha na verificação do domínio.',
        'pending_verification' => 'Aguardando verificação...',
    ],

    'dns' => [
        'title' => 'Configuração DNS',
        'instructions' => 'Adicione o seguinte registro DNS no seu provedor de domínio:',
        'record_type' => 'Tipo de Registro',
        'name' => 'Nome',
        'value' => 'Valor',
        'ttl' => 'TTL',
        'copy' => 'Copiar',
        'copied' => 'Copiado!',
        'note' => 'Nota: Pode levar até 24 horas para as alterações DNS serem propagadas.',
    ],

    'usage' => [
        'title' => 'Como Usar',
        'description' => 'Após a verificação, você pode usar seu domínio customizado para URLs curtas.',
        'example' => 'Exemplo:',
        'before' => 'Antes: qrsistema.com/u/abc123',
        'after' => 'Depois: seu-dominio.com/abc123',
        'benefits' => [
            'title' => 'Benefícios:',
            'branding' => 'Melhor branding',
            'trust' => 'Maior confiança',
            'professional' => 'Aparência profissional',
            'custom' => 'URLs personalizadas',
        ],
    ],

    'management' => [
        'title' => 'Gerenciar Domínios',
        'add_domain' => 'Adicionar Domínio',
        'primary_domain' => 'Domínio Primário',
        'secondary_domains' => 'Domínios Secundários',
        'no_domains' => 'Nenhum domínio customizado configurado.',
        'add_first' => 'Adicionar seu primeiro domínio',
        'limit_reached' => 'Limite de domínios atingido.',
        'upgrade_required' => 'Upgrade necessário para adicionar mais domínios.',
    ],

    'forms' => [
        'create' => [
            'title' => 'Adicionar Domínio Customizado',
            'domain' => 'Domínio',
            'domain_placeholder' => 'exemplo.com',
            'description' => 'Digite o domínio que você deseja usar (sem http:// ou https://)',
            'add' => 'Adicionar Domínio',
        ],
        'edit' => [
            'title' => 'Editar Domínio',
            'domain' => 'Domínio',
            'status' => 'Status',
            'update' => 'Atualizar Domínio',
        ],
    ],

    'messages' => [
        'created' => 'Domínio adicionado com sucesso!',
        'updated' => 'Domínio atualizado com sucesso!',
        'deleted' => 'Domínio excluído com sucesso!',
        'verified' => 'Domínio verificado com sucesso!',
        'set_primary' => 'Domínio definido como primário!',
        'verification_failed' => 'Falha na verificação do domínio.',
        'not_found' => 'Domínio não encontrado.',
        'access_denied' => 'Você não tem permissão para acessar este domínio.',
        'already_exists' => 'Este domínio já está em uso.',
        'invalid_domain' => 'Domínio inválido.',
        'dns_not_configured' => 'Registro DNS não configurado corretamente.',
        'verification_pending' => 'Verificação pendente. Aguarde a propagação DNS.',
        'cannot_delete_primary' => 'Não é possível excluir o domínio primário.',
        'limit_exceeded' => 'Limite de domínios excedido.',
    ],

    'instructions' => [
        'title' => 'Instruções de Configuração DNS',
        'step1' => [
            'title' => 'Passo 1: Acesse seu provedor de domínio',
            'description' => 'Faça login no painel de controle do seu provedor de domínio (GoDaddy, Namecheap, etc.)',
        ],
        'step2' => [
            'title' => 'Passo 2: Adicione o registro DNS',
            'description' => 'Adicione um registro CNAME ou TXT conforme mostrado abaixo:',
        ],
        'step3' => [
            'title' => 'Passo 3: Aguarde a propagação',
            'description' => 'Aguarde até 24 horas para a propagação DNS e clique em "Verificar"',
        ],
        'step4' => [
            'title' => 'Passo 4: Configure como primário',
            'description' => 'Após a verificação, configure o domínio como primário se desejar',
        ],
    ],

    'troubleshooting' => [
        'title' => 'Solução de Problemas',
        'common_issues' => [
            'title' => 'Problemas Comuns:',
            'dns_propagation' => 'DNS ainda não propagado (aguarde até 24h)',
            'wrong_record' => 'Registro DNS incorreto',
            'ttl_too_high' => 'TTL muito alto (recomendado: 300-3600)',
            'caching' => 'Cache DNS do navegador',
        ],
        'solutions' => [
            'title' => 'Soluções:',
            'check_dns' => 'Verifique o registro DNS com ferramentas online',
            'clear_cache' => 'Limpe o cache do navegador',
            'contact_support' => 'Entre em contato com o suporte se o problema persistir',
        ],
    ],

    'pricing' => [
        'title' => 'Preços',
        'free' => 'Gratuito',
        'premium' => 'Premium',
        'features' => [
            'free' => [
                '1_domain' => '1 domínio customizado',
                'basic_verification' => 'Verificação básica',
                'standard_support' => 'Suporte padrão',
            ],
            'premium' => [
                'unlimited_domains' => 'Domínios ilimitados',
                'advanced_verification' => 'Verificação avançada',
                'priority_support' => 'Suporte prioritário',
                'ssl_certificates' => 'Certificados SSL automáticos',
            ],
        ],
    ],

    'help' => [
        'title' => 'Ajuda com Domínios',
        'faq' => [
            'what_is_custom_domain' => 'O que é um domínio customizado?',
            'how_to_setup' => 'Como configurar um domínio customizado?',
            'verification_time' => 'Quanto tempo leva para verificar?',
            'multiple_domains' => 'Posso usar múltiplos domínios?',
            'ssl_certificates' => 'Os certificados SSL são incluídos?',
        ],
    ],

    'errors' => [
        'domain_required' => 'O domínio é obrigatório.',
        'domain_invalid' => 'Domínio inválido.',
        'domain_format' => 'Formato de domínio inválido.',
        'domain_taken' => 'Este domínio já está em uso.',
        'dns_record_required' => 'Registro DNS é obrigatório.',
        'verification_failed' => 'Falha na verificação.',
        'permission_denied' => 'Permissão negada.',
        'limit_exceeded' => 'Limite excedido.',
        'primary_required' => 'Pelo menos um domínio primário é necessário.',
    ],
];
