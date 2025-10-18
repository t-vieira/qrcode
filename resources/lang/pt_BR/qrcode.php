<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Linhas de Linguagem para QR Codes
    |--------------------------------------------------------------------------
    |
    | As seguintes linhas de linguagem são usadas para o sistema de QR Codes
    | incluindo tipos, validações, mensagens e interface do usuário.
    |
    */

    'title' => 'QR Codes',
    'create' => 'Criar QR Code',
    'edit' => 'Editar QR Code',
    'delete' => 'Excluir QR Code',
    'download' => 'Baixar QR Code',
    'preview' => 'Visualizar QR Code',
    'share' => 'Compartilhar QR Code',
    'stats' => 'Estatísticas',
    'export' => 'Exportar Relatório',

    'fields' => [
        'name' => 'Nome do QR Code',
        'type' => 'Tipo',
        'content' => 'Conteúdo',
        'design' => 'Design',
        'resolution' => 'Resolução',
        'format' => 'Formato',
        'folder' => 'Pasta',
        'team' => 'Equipe',
        'short_code' => 'Código Curto',
        'is_dynamic' => 'QR Code Dinâmico',
        'status' => 'Status',
        'scans_count' => 'Total de Scans',
        'unique_scans' => 'Scans Únicos',
        'created_at' => 'Criado em',
        'updated_at' => 'Atualizado em',
    ],

    'types' => [
        'url' => 'URL',
        'vcard' => 'vCard',
        'business' => 'Página de Negócio',
        'coupon' => 'Cupom',
        'text' => 'Texto Livre',
        'mp3' => 'MP3',
        'pdf' => 'PDF',
        'image' => 'Imagem',
        'video' => 'Vídeo',
        'app' => 'Aplicativo',
        'menu' => 'Cardápio Digital',
        'email' => 'E-mail',
        'phone' => 'Telefone',
        'sms' => 'SMS',
        'social' => 'Redes Sociais',
        'wifi' => 'Wi-Fi',
        'event' => 'Evento',
        'location' => 'Localização',
        'feedback' => 'Feedback',
        'crypto' => 'Carteira Crypto',
    ],

    'formats' => [
        'png' => 'PNG',
        'jpg' => 'JPG',
        'svg' => 'SVG',
        'eps' => 'EPS',
    ],

    'resolutions' => [
        '100' => '100x100 (Baixa)',
        '200' => '200x200 (Média)',
        '300' => '300x300 (Alta)',
        '500' => '500x500 (Muito Alta)',
        '1000' => '1000x1000 (Ultra Alta)',
        '2000' => '2000x2000 (Máxima)',
    ],

    'status' => [
        'active' => 'Ativo',
        'archived' => 'Arquivado',
        'deleted' => 'Excluído',
    ],

    'design' => [
        'title' => 'Personalização Visual',
        'colors' => 'Cores',
        'body_color' => 'Cor do Corpo',
        'border_color' => 'Cor da Borda',
        'eye_color' => 'Cor dos Olhos',
        'background_color' => 'Cor de Fundo',
        'logo' => 'Logo Central',
        'sticker' => 'Sticker/CTA',
        'shape' => 'Formato',
        'square' => 'Quadrado',
        'rounded' => 'Arredondado',
        'circle' => 'Circular',
    ],

    'content' => [
        'url' => [
            'url' => 'URL',
            'title' => 'Título (opcional)',
            'description' => 'Descrição (opcional)',
        ],
        'vcard' => [
            'first_name' => 'Nome',
            'last_name' => 'Sobrenome',
            'organization' => 'Organização',
            'title' => 'Cargo',
            'phone' => 'Telefone',
            'email' => 'E-mail',
            'website' => 'Site',
            'address' => 'Endereço',
            'city' => 'Cidade',
            'state' => 'Estado',
            'zip' => 'CEP',
            'country' => 'País',
        ],
        'business' => [
            'name' => 'Nome do Negócio',
            'description' => 'Descrição',
            'phone' => 'Telefone',
            'email' => 'E-mail',
            'website' => 'Site',
            'address' => 'Endereço',
            'hours' => 'Horário de Funcionamento',
            'services' => 'Serviços',
        ],
        'coupon' => [
            'title' => 'Título do Cupom',
            'description' => 'Descrição',
            'discount' => 'Desconto',
            'valid_until' => 'Válido até',
            'terms' => 'Termos e Condições',
        ],
        'text' => [
            'text' => 'Texto',
        ],
        'email' => [
            'email' => 'E-mail',
            'subject' => 'Assunto',
            'body' => 'Corpo da Mensagem',
        ],
        'phone' => [
            'phone' => 'Número do Telefone',
        ],
        'sms' => [
            'phone' => 'Número do Telefone',
            'message' => 'Mensagem',
        ],
        'wifi' => [
            'ssid' => 'Nome da Rede (SSID)',
            'password' => 'Senha',
            'security' => 'Tipo de Segurança',
            'hidden' => 'Rede Ocultada',
        ],
        'social' => [
            'platform' => 'Plataforma',
            'username' => 'Nome de Usuário',
            'url' => 'URL do Perfil',
        ],
        'event' => [
            'title' => 'Título do Evento',
            'description' => 'Descrição',
            'start_date' => 'Data de Início',
            'end_date' => 'Data de Fim',
            'location' => 'Local',
            'organizer' => 'Organizador',
        ],
        'location' => [
            'name' => 'Nome do Local',
            'address' => 'Endereço',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
        ],
        'feedback' => [
            'title' => 'Título do Feedback',
            'description' => 'Descrição',
            'rating_scale' => 'Escala de Avaliação',
            'questions' => 'Perguntas',
        ],
        'crypto' => [
            'currency' => 'Moeda',
            'address' => 'Endereço da Carteira',
            'amount' => 'Valor (opcional)',
            'label' => 'Rótulo (opcional)',
        ],
    ],

    'messages' => [
        'created' => 'QR Code criado com sucesso!',
        'updated' => 'QR Code atualizado com sucesso!',
        'deleted' => 'QR Code excluído com sucesso!',
        'archived' => 'QR Code arquivado com sucesso!',
        'restored' => 'QR Code restaurado com sucesso!',
        'downloaded' => 'QR Code baixado com sucesso!',
        'shared' => 'QR Code compartilhado com sucesso!',
        'exported' => 'Relatório exportado com sucesso!',
        'not_found' => 'QR Code não encontrado.',
        'access_denied' => 'Você não tem permissão para acessar este QR Code.',
        'dynamic_required' => 'Esta funcionalidade requer um QR Code dinâmico.',
        'subscription_required' => 'Esta funcionalidade requer uma assinatura ativa.',
    ],

    'stats' => [
        'title' => 'Estatísticas do QR Code',
        'total_scans' => 'Total de Scans',
        'unique_scans' => 'Scans Únicos',
        'scans_today' => 'Scans Hoje',
        'scans_this_week' => 'Scans Esta Semana',
        'scans_this_month' => 'Scans Este Mês',
        'top_countries' => 'Principais Países',
        'top_cities' => 'Principais Cidades',
        'device_types' => 'Tipos de Dispositivo',
        'browsers' => 'Navegadores',
        'operating_systems' => 'Sistemas Operacionais',
        'scan_timeline' => 'Linha do Tempo de Scans',
        'no_data' => 'Nenhum dado de scan disponível ainda.',
    ],

    'export' => [
        'title' => 'Exportar Relatório',
        'format' => 'Formato',
        'date_range' => 'Período',
        'include' => 'Incluir',
        'all_data' => 'Todos os Dados',
        'summary_only' => 'Apenas Resumo',
        'download' => 'Baixar Relatório',
        'csv' => 'CSV',
        'excel' => 'Excel',
        'pdf' => 'PDF',
    ],

    'validation' => [
        'name_required' => 'O nome do QR Code é obrigatório.',
        'type_required' => 'O tipo do QR Code é obrigatório.',
        'content_required' => 'O conteúdo do QR Code é obrigatório.',
        'short_code_unique' => 'Este código curto já está em uso.',
        'short_code_format' => 'O código curto deve conter apenas letras, números, hífens e sublinhados.',
        'resolution_range' => 'A resolução deve estar entre 100 e 2000 pixels.',
        'format_supported' => 'Formato não suportado.',
        'logo_size' => 'O logo deve ter no máximo 30% do tamanho do QR Code.',
        'file_upload' => 'Erro no upload do arquivo.',
    ],
];
