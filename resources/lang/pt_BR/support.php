<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Linhas de Linguagem para Suporte
    |--------------------------------------------------------------------------
    |
    | As seguintes linhas de linguagem são usadas para o sistema de suporte
    | incluindo tickets, WhatsApp e comunicação com clientes.
    |
    */

    'title' => 'Suporte',
    'contact' => 'Contato',
    'ticket' => 'Ticket',
    'tickets' => 'Tickets',
    'create_ticket' => 'Criar Ticket',
    'reply' => 'Responder',
    'close' => 'Fechar',
    'reopen' => 'Reabrir',
    'status' => 'Status',

    'fields' => [
        'subject' => 'Assunto',
        'message' => 'Mensagem',
        'priority' => 'Prioridade',
        'status' => 'Status',
        'category' => 'Categoria',
        'created_at' => 'Criado em',
        'updated_at' => 'Atualizado em',
        'last_reply_at' => 'Última Resposta',
    ],

    'status' => [
        'open' => 'Aberto',
        'pending' => 'Pendente',
        'closed' => 'Fechado',
        'resolved' => 'Resolvido',
    ],

    'priority' => [
        'low' => 'Baixa',
        'medium' => 'Média',
        'high' => 'Alta',
        'urgent' => 'Urgente',
    ],

    'categories' => [
        'technical' => 'Técnico',
        'billing' => 'Faturamento',
        'feature_request' => 'Solicitação de Funcionalidade',
        'bug_report' => 'Relatório de Bug',
        'general' => 'Geral',
        'account' => 'Conta',
        'subscription' => 'Assinatura',
    ],

    'channels' => [
        'whatsapp' => 'WhatsApp',
        'email' => 'E-mail',
        'chat' => 'Chat',
        'phone' => 'Telefone',
    ],

    'whatsapp' => [
        'title' => 'Suporte via WhatsApp',
        'description' => 'Entre em contato conosco via WhatsApp para suporte rápido',
        'phone' => 'Telefone',
        'message' => 'Mensagem',
        'send' => 'Enviar Mensagem',
        'business_hours' => 'Horário de Funcionamento',
        'response_time' => 'Tempo de Resposta',
        'online' => 'Online',
        'offline' => 'Offline',
        'typing' => 'Digitando...',
        'message_sent' => 'Mensagem enviada com sucesso!',
        'message_received' => 'Nova mensagem recebida',
    ],

    'tickets' => [
        'title' => 'Meus Tickets',
        'no_tickets' => 'Nenhum ticket encontrado.',
        'create_first' => 'Criar seu primeiro ticket',
        'view_ticket' => 'Ver Ticket',
        'reply_to_ticket' => 'Responder ao Ticket',
        'close_ticket' => 'Fechar Ticket',
        'reopen_ticket' => 'Reabrir Ticket',
        'ticket_closed' => 'Ticket fechado',
        'ticket_reopened' => 'Ticket reaberto',
    ],

    'conversation' => [
        'title' => 'Conversa',
        'agent' => 'Agente',
        'customer' => 'Cliente',
        'system' => 'Sistema',
        'typing' => 'Digitando...',
        'online' => 'Online',
        'last_seen' => 'Visto por último',
        'send_message' => 'Enviar mensagem',
        'attach_file' => 'Anexar arquivo',
        'emoji' => 'Emoji',
        'message_placeholder' => 'Digite sua mensagem...',
    ],

    'forms' => [
        'create_ticket' => [
            'title' => 'Criar Novo Ticket',
            'subject' => 'Assunto',
            'category' => 'Categoria',
            'priority' => 'Prioridade',
            'message' => 'Mensagem',
            'attach_files' => 'Anexar Arquivos',
            'create' => 'Criar Ticket',
        ],
        'reply' => [
            'title' => 'Responder ao Ticket',
            'message' => 'Sua Resposta',
            'attach_files' => 'Anexar Arquivos',
            'reply' => 'Responder',
            'close_after_reply' => 'Fechar ticket após resposta',
        ],
    ],

    'messages' => [
        'ticket_created' => 'Ticket criado com sucesso!',
        'ticket_updated' => 'Ticket atualizado com sucesso!',
        'ticket_closed' => 'Ticket fechado com sucesso!',
        'ticket_reopened' => 'Ticket reaberto com sucesso!',
        'reply_sent' => 'Resposta enviada com sucesso!',
        'message_sent' => 'Mensagem enviada com sucesso!',
        'file_uploaded' => 'Arquivo anexado com sucesso!',
        'not_found' => 'Ticket não encontrado.',
        'access_denied' => 'Você não tem permissão para acessar este ticket.',
        'already_closed' => 'Este ticket já está fechado.',
        'already_open' => 'Este ticket já está aberto.',
    ],

    'notifications' => [
        'new_ticket' => [
            'title' => 'Novo Ticket',
            'message' => 'Você recebeu um novo ticket de suporte.',
        ],
        'ticket_reply' => [
            'title' => 'Resposta no Ticket',
            'message' => 'Você recebeu uma resposta no seu ticket.',
        ],
        'ticket_closed' => [
            'title' => 'Ticket Fechado',
            'message' => 'Seu ticket foi fechado.',
        ],
        'ticket_reopened' => [
            'title' => 'Ticket Reaberto',
            'message' => 'Seu ticket foi reaberto.',
        ],
    ],

    'faq' => [
        'title' => 'Perguntas Frequentes',
        'search' => 'Buscar FAQ',
        'categories' => 'Categorias',
        'popular' => 'Populares',
        'recent' => 'Recentes',
        'no_results' => 'Nenhuma pergunta encontrada.',
        'was_helpful' => 'Esta resposta foi útil?',
        'yes' => 'Sim',
        'no' => 'Não',
        'thank_you' => 'Obrigado pelo seu feedback!',
    ],

    'knowledge_base' => [
        'title' => 'Base de Conhecimento',
        'articles' => 'Artigos',
        'tutorials' => 'Tutoriais',
        'guides' => 'Guias',
        'search' => 'Buscar na Base de Conhecimento',
        'categories' => 'Categorias',
        'tags' => 'Tags',
        'related_articles' => 'Artigos Relacionados',
        'table_of_contents' => 'Índice',
    ],

    'satisfaction' => [
        'title' => 'Avaliação do Atendimento',
        'how_was_support' => 'Como foi o nosso atendimento?',
        'very_satisfied' => 'Muito Satisfeito',
        'satisfied' => 'Satisfeito',
        'neutral' => 'Neutro',
        'dissatisfied' => 'Insatisfeito',
        'very_dissatisfied' => 'Muito Insatisfeito',
        'feedback' => 'Comentários (opcional)',
        'submit' => 'Enviar Avaliação',
        'thank_you' => 'Obrigado pela sua avaliação!',
    ],

    'business_hours' => [
        'title' => 'Horário de Funcionamento',
        'monday' => 'Segunda-feira',
        'tuesday' => 'Terça-feira',
        'wednesday' => 'Quarta-feira',
        'thursday' => 'Quinta-feira',
        'friday' => 'Sexta-feira',
        'saturday' => 'Sábado',
        'sunday' => 'Domingo',
        'closed' => 'Fechado',
        'open_24_7' => 'Aberto 24/7',
        'current_status' => 'Status Atual',
        'next_open' => 'Próxima Abertura',
    ],

    'contact_info' => [
        'title' => 'Informações de Contato',
        'phone' => 'Telefone',
        'email' => 'E-mail',
        'whatsapp' => 'WhatsApp',
        'address' => 'Endereço',
        'website' => 'Site',
        'social_media' => 'Redes Sociais',
    ],

    'help' => [
        'title' => 'Ajuda com Suporte',
        'how_to_create_ticket' => 'Como criar um ticket?',
        'how_to_contact' => 'Como entrar em contato?',
        'response_times' => 'Quais são os tempos de resposta?',
        'business_hours' => 'Qual é o horário de funcionamento?',
        'escalation' => 'Como escalar um problema?',
    ],

    'errors' => [
        'subject_required' => 'O assunto é obrigatório.',
        'message_required' => 'A mensagem é obrigatória.',
        'category_required' => 'A categoria é obrigatória.',
        'priority_required' => 'A prioridade é obrigatória.',
        'file_too_large' => 'Arquivo muito grande.',
        'file_type_not_allowed' => 'Tipo de arquivo não permitido.',
        'message_too_long' => 'Mensagem muito longa.',
        'rate_limit_exceeded' => 'Muitas mensagens enviadas. Tente novamente em alguns minutos.',
        'whatsapp_not_available' => 'WhatsApp não está disponível no momento.',
        'ticket_not_found' => 'Ticket não encontrado.',
        'invalid_status' => 'Status inválido.',
        'invalid_priority' => 'Prioridade inválida.',
    ],
];
