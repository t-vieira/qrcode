<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Linhas de Linguagem para Dashboard
    |--------------------------------------------------------------------------
    |
    | As seguintes linhas de linguagem são usadas para o dashboard principal
    | incluindo estatísticas, gráficos e informações gerais.
    |
    */

    'title' => 'Dashboard',
    'welcome' => 'Bem-vindo de volta, :name!',
    'overview' => 'Visão Geral',
    'quick_actions' => 'Ações Rápidas',
    'recent_activity' => 'Atividade Recente',
    'statistics' => 'Estatísticas',

    'stats' => [
        'total_qr_codes' => 'Total de QR Codes',
        'total_scans' => 'Total de Scans',
        'unique_scans' => 'Scans Únicos',
        'scans_today' => 'Scans Hoje',
        'scans_this_week' => 'Scans Esta Semana',
        'scans_this_month' => 'Scans Este Mês',
        'most_scanned' => 'Mais Escaneado',
        'recent_qr_codes' => 'QR Codes Recentes',
        'subscription_status' => 'Status da Assinatura',
        'trial_remaining' => 'Trial Restante',
    ],

    'charts' => [
        'scans_over_time' => 'Scans ao Longo do Tempo',
        'scans_by_device' => 'Scans por Dispositivo',
        'scans_by_country' => 'Scans por País',
        'scans_by_city' => 'Scans por Cidade',
        'top_qr_codes' => 'QR Codes Mais Escaneados',
        'scan_timeline' => 'Linha do Tempo de Scans',
    ],

    'quick_actions' => [
        'create_qr_code' => 'Criar QR Code',
        'view_all_qr_codes' => 'Ver Todos os QR Codes',
        'create_folder' => 'Criar Pasta',
        'view_statistics' => 'Ver Estatísticas',
        'export_reports' => 'Exportar Relatórios',
        'manage_team' => 'Gerenciar Equipe',
        'custom_domains' => 'Domínios Customizados',
        'support' => 'Suporte',
    ],

    'recent_qr_codes' => [
        'title' => 'QR Codes Recentes',
        'name' => 'Nome',
        'type' => 'Tipo',
        'scans' => 'Scans',
        'created' => 'Criado',
        'actions' => 'Ações',
        'view' => 'Ver',
        'edit' => 'Editar',
        'download' => 'Baixar',
        'share' => 'Compartilhar',
        'no_qr_codes' => 'Nenhum QR Code encontrado.',
        'create_first' => 'Criar seu primeiro QR Code',
    ],

    'subscription_info' => [
        'title' => 'Informações da Assinatura',
        'current_plan' => 'Plano Atual',
        'status' => 'Status',
        'next_billing' => 'Próxima Cobrança',
        'trial_ends' => 'Trial Termina',
        'upgrade' => 'Fazer Upgrade',
        'manage' => 'Gerenciar Assinatura',
        'active' => 'Ativa',
        'trialing' => 'Em Teste',
        'expired' => 'Expirada',
        'cancelled' => 'Cancelada',
    ],

    'notifications' => [
        'title' => 'Notificações',
        'no_notifications' => 'Nenhuma notificação.',
        'mark_all_read' => 'Marcar todas como lidas',
        'trial_expiring' => 'Seu período de teste expira em :days dias.',
        'trial_expired' => 'Seu período de teste expirou.',
        'payment_failed' => 'Falha no pagamento. Verifique seus dados.',
        'subscription_cancelled' => 'Sua assinatura foi cancelada.',
        'new_feature' => 'Nova funcionalidade disponível!',
        'maintenance' => 'Manutenção programada em :date.',
    ],

    'tips' => [
        'title' => 'Dicas',
        'tip1' => 'Use QR Codes dinâmicos para poder editar o conteúdo sem alterar o código físico.',
        'tip2' => 'Personalize seus QR Codes com cores e logos para melhorar o reconhecimento da marca.',
        'tip3' => 'Acompanhe as estatísticas para entender melhor seu público.',
        'tip4' => 'Organize seus QR Codes em pastas para facilitar o gerenciamento.',
        'tip5' => 'Use domínios customizados para URLs mais profissionais.',
    ],

    'widgets' => [
        'qr_code_creator' => [
            'title' => 'Criador de QR Code',
            'description' => 'Crie um novo QR Code rapidamente',
            'create' => 'Criar QR Code',
        ],
        'quick_stats' => [
            'title' => 'Estatísticas Rápidas',
            'description' => 'Visão geral das suas métricas',
        ],
        'recent_scans' => [
            'title' => 'Scans Recentes',
            'description' => 'Últimos scans dos seus QR Codes',
            'no_scans' => 'Nenhum scan recente.',
        ],
        'top_performers' => [
            'title' => 'Melhores Performances',
            'description' => 'Seus QR Codes mais escaneados',
            'no_data' => 'Nenhum dado disponível ainda.',
        ],
    ],

    'filters' => [
        'date_range' => 'Período',
        'last_7_days' => 'Últimos 7 dias',
        'last_30_days' => 'Últimos 30 dias',
        'last_90_days' => 'Últimos 90 dias',
        'this_year' => 'Este ano',
        'custom' => 'Personalizado',
        'apply' => 'Aplicar',
        'reset' => 'Resetar',
    ],

    'export' => [
        'title' => 'Exportar Dados',
        'description' => 'Baixe relatórios detalhados dos seus QR Codes',
        'format' => 'Formato',
        'date_range' => 'Período',
        'include' => 'Incluir',
        'download' => 'Baixar',
        'csv' => 'CSV',
        'excel' => 'Excel',
        'pdf' => 'PDF',
    ],

    'empty_state' => [
        'title' => 'Bem-vindo ao QR Code SaaS!',
        'description' => 'Comece criando seu primeiro QR Code para ver as estatísticas e funcionalidades.',
        'create_qr_code' => 'Criar Primeiro QR Code',
        'learn_more' => 'Saiba Mais',
    ],

    'loading' => [
        'loading_stats' => 'Carregando estatísticas...',
        'loading_charts' => 'Carregando gráficos...',
        'loading_data' => 'Carregando dados...',
    ],

    'errors' => [
        'load_failed' => 'Falha ao carregar dados do dashboard.',
        'stats_unavailable' => 'Estatísticas temporariamente indisponíveis.',
        'chart_error' => 'Erro ao carregar gráfico.',
        'export_failed' => 'Falha ao exportar dados.',
    ],

    'help' => [
        'title' => 'Precisa de Ajuda?',
        'description' => 'Confira nossos tutoriais e FAQ para aproveitar ao máximo o sistema.',
        'tutorials' => 'Tutoriais',
        'faq' => 'FAQ',
        'contact' => 'Contato',
    ],
];
