<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Linhas de Linguagem para Equipes
    |--------------------------------------------------------------------------
    |
    | As seguintes linhas de linguagem são usadas para o sistema de equipes
    | incluindo gerenciamento de membros, permissões e colaboração.
    |
    */

    'title' => 'Equipes',
    'create' => 'Criar Equipe',
    'edit' => 'Editar Equipe',
    'delete' => 'Excluir Equipe',
    'join' => 'Entrar na Equipe',
    'leave' => 'Sair da Equipe',
    'invite' => 'Convidar Membro',
    'remove' => 'Remover Membro',

    'fields' => [
        'name' => 'Nome da Equipe',
        'description' => 'Descrição',
        'owner' => 'Proprietário',
        'members' => 'Membros',
        'role' => 'Função',
        'permissions' => 'Permissões',
        'created_at' => 'Criado em',
        'updated_at' => 'Atualizado em',
    ],

    'roles' => [
        'owner' => 'Proprietário',
        'admin' => 'Administrador',
        'editor' => 'Editor',
        'viewer' => 'Visualizador',
    ],

    'permissions' => [
        'view_qr_codes' => 'Visualizar QR Codes',
        'create_qr_codes' => 'Criar QR Codes',
        'edit_qr_codes' => 'Editar QR Codes',
        'delete_qr_codes' => 'Excluir QR Codes',
        'view_statistics' => 'Visualizar Estatísticas',
        'export_reports' => 'Exportar Relatórios',
        'manage_folders' => 'Gerenciar Pastas',
        'manage_team' => 'Gerenciar Equipe',
        'invite_members' => 'Convidar Membros',
        'remove_members' => 'Remover Membros',
    ],

    'invitation' => [
        'title' => 'Convidar Membro',
        'email' => 'E-mail do Membro',
        'role' => 'Função',
        'message' => 'Mensagem (opcional)',
        'send_invitation' => 'Enviar Convite',
        'invitation_sent' => 'Convite enviado com sucesso!',
        'invitation_accepted' => 'Convite aceito com sucesso!',
        'invitation_declined' => 'Convite recusado.',
        'invitation_expired' => 'Convite expirado.',
    ],

    'members' => [
        'title' => 'Membros da Equipe',
        'name' => 'Nome',
        'email' => 'E-mail',
        'role' => 'Função',
        'joined_at' => 'Entrou em',
        'last_active' => 'Última Atividade',
        'actions' => 'Ações',
        'change_role' => 'Alterar Função',
        'remove_member' => 'Remover Membro',
        'no_members' => 'Nenhum membro encontrado.',
    ],

    'qr_codes' => [
        'title' => 'QR Codes da Equipe',
        'shared_with_team' => 'Compartilhado com a Equipe',
        'shared_by' => 'Compartilhado por',
        'no_shared_qr_codes' => 'Nenhum QR Code compartilhado com a equipe.',
    ],

    'folders' => [
        'title' => 'Pastas da Equipe',
        'shared_folders' => 'Pastas Compartilhadas',
        'no_shared_folders' => 'Nenhuma pasta compartilhada.',
    ],

    'messages' => [
        'created' => 'Equipe criada com sucesso!',
        'updated' => 'Equipe atualizada com sucesso!',
        'deleted' => 'Equipe excluída com sucesso!',
        'member_added' => 'Membro adicionado com sucesso!',
        'member_removed' => 'Membro removido com sucesso!',
        'role_updated' => 'Função atualizada com sucesso!',
        'invitation_sent' => 'Convite enviado com sucesso!',
        'joined' => 'Você entrou na equipe com sucesso!',
        'left' => 'Você saiu da equipe com sucesso!',
        'not_found' => 'Equipe não encontrada.',
        'access_denied' => 'Você não tem permissão para acessar esta equipe.',
        'already_member' => 'Você já é membro desta equipe.',
        'not_member' => 'Você não é membro desta equipe.',
        'cannot_remove_owner' => 'Não é possível remover o proprietário da equipe.',
        'cannot_leave_owned_team' => 'Você não pode sair de uma equipe que você possui.',
    ],

    'forms' => [
        'create' => [
            'title' => 'Criar Nova Equipe',
            'name' => 'Nome da Equipe',
            'description' => 'Descrição (opcional)',
            'create' => 'Criar Equipe',
        ],
        'edit' => [
            'title' => 'Editar Equipe',
            'name' => 'Nome da Equipe',
            'description' => 'Descrição',
            'update' => 'Atualizar Equipe',
        ],
        'invite' => [
            'title' => 'Convidar Membro',
            'email' => 'E-mail do Membro',
            'role' => 'Função',
            'message' => 'Mensagem de boas-vindas (opcional)',
            'send' => 'Enviar Convite',
        ],
    ],

    'settings' => [
        'title' => 'Configurações da Equipe',
        'general' => 'Geral',
        'permissions' => 'Permissões',
        'notifications' => 'Notificações',
        'danger_zone' => 'Zona de Perigo',
        'delete_team' => 'Excluir Equipe',
        'delete_team_description' => 'Esta ação é irreversível. Todos os dados da equipe serão perdidos.',
        'confirm_delete' => 'Sim, excluir equipe',
    ],

    'notifications' => [
        'invited_to_team' => [
            'title' => 'Convite para Equipe',
            'message' => 'Você foi convidado para a equipe :team_name.',
        ],
        'member_joined' => [
            'title' => 'Novo Membro',
            'message' => ':name entrou na equipe :team_name.',
        ],
        'member_left' => [
            'title' => 'Membro Saiu',
            'message' => ':name saiu da equipe :team_name.',
        ],
        'role_changed' => [
            'title' => 'Função Alterada',
            'message' => 'Sua função na equipe :team_name foi alterada para :role.',
        ],
    ],

    'help' => [
        'title' => 'Ajuda com Equipes',
        'faq' => [
            'what_is_team' => 'O que é uma equipe?',
            'how_to_invite' => 'Como convidar membros?',
            'permissions' => 'Como funcionam as permissões?',
            'leave_team' => 'Como sair de uma equipe?',
            'delete_team' => 'Como excluir uma equipe?',
        ],
    ],

    'errors' => [
        'name_required' => 'O nome da equipe é obrigatório.',
        'name_unique' => 'Já existe uma equipe com este nome.',
        'email_required' => 'O e-mail é obrigatório.',
        'email_valid' => 'O e-mail deve ser válido.',
        'role_required' => 'A função é obrigatória.',
        'role_valid' => 'A função deve ser válida.',
        'cannot_invite_self' => 'Você não pode convidar a si mesmo.',
        'user_already_member' => 'Este usuário já é membro da equipe.',
        'user_not_found' => 'Usuário não encontrado.',
        'invitation_not_found' => 'Convite não encontrado.',
        'invitation_expired' => 'Convite expirado.',
        'insufficient_permissions' => 'Permissões insuficientes.',
    ],
];
