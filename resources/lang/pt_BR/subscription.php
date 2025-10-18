<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Linhas de Linguagem para Assinaturas
    |--------------------------------------------------------------------------
    |
    | As seguintes linhas de linguagem são usadas para o sistema de assinaturas
    | incluindo planos, pagamentos, status e mensagens relacionadas.
    |
    */

    'title' => 'Assinatura',
    'upgrade' => 'Fazer Upgrade',
    'subscribe' => 'Assinar',
    'cancel' => 'Cancelar Assinatura',
    'renew' => 'Renovar Assinatura',
    'change_plan' => 'Alterar Plano',
    'billing' => 'Faturamento',
    'payment_method' => 'Método de Pagamento',

    'status' => [
        'trialing' => 'Período de Teste',
        'active' => 'Ativa',
        'canceled' => 'Cancelada',
        'expired' => 'Expirada',
        'pending' => 'Pendente',
        'authorized' => 'Autorizada',
        'paused' => 'Pausada',
        'cancelled' => 'Cancelada',
    ],

    'plans' => [
        'free' => [
            'name' => 'Gratuito',
            'description' => 'Período de teste de 7 dias',
            'features' => [
                'QR Codes ilimitados',
                'QR Codes estáticos',
                'Personalização básica',
                'Suporte por email',
            ],
            'price' => 'Grátis',
            'period' => '7 dias',
        ],
        'premium' => [
            'name' => 'Premium',
            'description' => 'Acesso completo a todas as funcionalidades',
            'features' => [
                'QR Codes ilimitados',
                'QR Codes dinâmicos',
                'Estatísticas avançadas',
                'Domínios customizados',
                'Sistema de equipes',
                'Suporte prioritário',
                'Exportação de relatórios',
                'API completa',
            ],
            'price' => 'R$ 29,90',
            'period' => 'por mês',
        ],
    ],

    'trial' => [
        'title' => 'Período de Teste',
        'description' => 'Você tem 7 dias grátis para testar todas as funcionalidades',
        'remaining' => 'Restam :days dias do seu período de teste',
        'expired' => 'Seu período de teste expirou',
        'expires_at' => 'Expira em :date',
        'upgrade_now' => 'Fazer Upgrade Agora',
        'features_locked' => 'Algumas funcionalidades estão bloqueadas após o período de teste',
    ],

    'payment' => [
        'title' => 'Pagamento',
        'method' => 'Método de Pagamento',
        'card' => 'Cartão de Crédito',
        'pix' => 'PIX',
        'boleto' => 'Boleto Bancário',
        'credit_card' => 'Cartão de Crédito',
        'debit_card' => 'Cartão de Débito',
        'processing' => 'Processando pagamento...',
        'success' => 'Pagamento processado com sucesso!',
        'failed' => 'Falha no processamento do pagamento.',
        'pending' => 'Pagamento pendente de confirmação.',
        'cancelled' => 'Pagamento cancelado.',
        'refunded' => 'Pagamento estornado.',
    ],

    'billing' => [
        'title' => 'Faturamento',
        'current_plan' => 'Plano Atual',
        'next_billing' => 'Próxima Cobrança',
        'billing_history' => 'Histórico de Faturamento',
        'invoice' => 'Fatura',
        'amount' => 'Valor',
        'date' => 'Data',
        'status' => 'Status',
        'download' => 'Baixar Fatura',
        'no_invoices' => 'Nenhuma fatura encontrada.',
    ],

    'webhook' => [
        'processing' => 'Processando webhook de pagamento...',
        'success' => 'Webhook processado com sucesso.',
        'failed' => 'Falha ao processar webhook.',
        'invalid' => 'Webhook inválido.',
        'unauthorized' => 'Webhook não autorizado.',
    ],

    'messages' => [
        'subscribed' => 'Assinatura ativada com sucesso!',
        'cancelled' => 'Assinatura cancelada com sucesso!',
        'renewed' => 'Assinatura renovada com sucesso!',
        'plan_changed' => 'Plano alterado com sucesso!',
        'payment_updated' => 'Método de pagamento atualizado com sucesso!',
        'trial_started' => 'Período de teste iniciado!',
        'trial_expired' => 'Período de teste expirado.',
        'subscription_expired' => 'Assinatura expirada.',
        'payment_failed' => 'Falha no pagamento. Verifique seus dados.',
        'payment_retry' => 'Tentando processar o pagamento novamente...',
        'access_denied' => 'Acesso negado. Assinatura necessária.',
        'feature_locked' => 'Esta funcionalidade está bloqueada. Faça upgrade para acessar.',
    ],

    'notifications' => [
        'trial_expiring' => [
            'title' => 'Período de teste expirando',
            'message' => 'Seu período de teste expira em :days dias. Faça upgrade para continuar usando todas as funcionalidades.',
        ],
        'trial_expired' => [
            'title' => 'Período de teste expirado',
            'message' => 'Seu período de teste expirou. Faça upgrade para continuar usando o sistema.',
        ],
        'payment_failed' => [
            'title' => 'Falha no pagamento',
            'message' => 'Não foi possível processar seu pagamento. Verifique seus dados e tente novamente.',
        ],
        'subscription_cancelled' => [
            'title' => 'Assinatura cancelada',
            'message' => 'Sua assinatura foi cancelada. Você ainda pode usar o sistema até o fim do período atual.',
        ],
    ],

    'forms' => [
        'upgrade' => [
            'title' => 'Fazer Upgrade',
            'description' => 'Escolha o plano que melhor atende suas necessidades',
            'select_plan' => 'Selecionar Plano',
            'payment_method' => 'Método de Pagamento',
            'terms' => 'Ao fazer upgrade, você concorda com nossos :terms e :privacy',
            'terms_link' => 'Termos de Uso',
            'privacy_link' => 'Política de Privacidade',
            'subscribe' => 'Assinar Agora',
        ],
        'cancel' => [
            'title' => 'Cancelar Assinatura',
            'description' => 'Tem certeza que deseja cancelar sua assinatura?',
            'reason' => 'Motivo do cancelamento (opcional)',
            'feedback' => 'Gostaríamos de saber por que você está cancelando para melhorarmos nosso serviço.',
            'confirm' => 'Sim, cancelar assinatura',
            'keep' => 'Manter assinatura',
        ],
        'payment' => [
            'card_number' => 'Número do Cartão',
            'expiry_date' => 'Data de Vencimento',
            'cvv' => 'CVV',
            'cardholder_name' => 'Nome do Portador',
            'billing_address' => 'Endereço de Cobrança',
            'save_card' => 'Salvar cartão para futuras compras',
        ],
    ],

    'errors' => [
        'plan_not_found' => 'Plano não encontrado.',
        'payment_method_required' => 'Método de pagamento é obrigatório.',
        'invalid_payment_method' => 'Método de pagamento inválido.',
        'payment_processing_failed' => 'Falha no processamento do pagamento.',
        'subscription_not_found' => 'Assinatura não encontrada.',
        'subscription_already_active' => 'Assinatura já está ativa.',
        'subscription_already_cancelled' => 'Assinatura já foi cancelada.',
        'trial_already_used' => 'Período de teste já foi utilizado.',
        'upgrade_required' => 'Upgrade necessário para acessar esta funcionalidade.',
    ],

    'help' => [
        'title' => 'Ajuda com Assinaturas',
        'faq' => [
            'how_to_upgrade' => 'Como fazer upgrade?',
            'how_to_cancel' => 'Como cancelar minha assinatura?',
            'when_charged' => 'Quando serei cobrado?',
            'payment_methods' => 'Quais métodos de pagamento são aceitos?',
            'refund_policy' => 'Qual é a política de reembolso?',
            'trial_period' => 'Como funciona o período de teste?',
        ],
        'contact' => 'Precisa de ajuda? Entre em contato conosco.',
    ],
];
