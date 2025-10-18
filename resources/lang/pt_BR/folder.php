<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Linhas de Linguagem para Pastas
    |--------------------------------------------------------------------------
    |
    | As seguintes linhas de linguagem são usadas para o sistema de pastas
    | incluindo organização, gerenciamento e estrutura hierárquica.
    |
    */

    'title' => 'Pastas',
    'create' => 'Criar Pasta',
    'edit' => 'Editar Pasta',
    'delete' => 'Excluir Pasta',
    'move' => 'Mover Pasta',
    'copy' => 'Copiar Pasta',
    'archive' => 'Arquivar Pasta',
    'restore' => 'Restaurar Pasta',

    'fields' => [
        'name' => 'Nome da Pasta',
        'description' => 'Descrição',
        'parent' => 'Pasta Pai',
        'qr_codes_count' => 'QR Codes',
        'created_at' => 'Criado em',
        'updated_at' => 'Atualizado em',
    ],

    'types' => [
        'personal' => 'Pessoal',
        'shared' => 'Compartilhada',
        'team' => 'Equipe',
    ],

    'status' => [
        'active' => 'Ativa',
        'archived' => 'Arquivada',
        'deleted' => 'Excluída',
    ],

    'tree' => [
        'root' => 'Raiz',
        'subfolder' => 'Subpasta',
        'expand' => 'Expandir',
        'collapse' => 'Recolher',
        'no_folders' => 'Nenhuma pasta encontrada.',
        'create_first' => 'Criar sua primeira pasta',
    ],

    'qr_codes' => [
        'title' => 'QR Codes na Pasta',
        'move_to_folder' => 'Mover para Pasta',
        'copy_to_folder' => 'Copiar para Pasta',
        'remove_from_folder' => 'Remover da Pasta',
        'no_qr_codes' => 'Nenhum QR Code nesta pasta.',
        'select_all' => 'Selecionar Todos',
        'deselect_all' => 'Desselecionar Todos',
    ],

    'actions' => [
        'bulk_move' => 'Mover Selecionados',
        'bulk_copy' => 'Copiar Selecionados',
        'bulk_delete' => 'Excluir Selecionados',
        'bulk_archive' => 'Arquivar Selecionados',
        'bulk_restore' => 'Restaurar Selecionados',
    ],

    'messages' => [
        'created' => 'Pasta criada com sucesso!',
        'updated' => 'Pasta atualizada com sucesso!',
        'deleted' => 'Pasta excluída com sucesso!',
        'moved' => 'Pasta movida com sucesso!',
        'copied' => 'Pasta copiada com sucesso!',
        'archived' => 'Pasta arquivada com sucesso!',
        'restored' => 'Pasta restaurada com sucesso!',
        'qr_codes_moved' => 'QR Codes movidos com sucesso!',
        'qr_codes_copied' => 'QR Codes copiados com sucesso!',
        'qr_codes_removed' => 'QR Codes removidos da pasta com sucesso!',
        'not_found' => 'Pasta não encontrada.',
        'access_denied' => 'Você não tem permissão para acessar esta pasta.',
        'cannot_delete_with_qr_codes' => 'Não é possível excluir pasta que contém QR Codes.',
        'cannot_move_to_self' => 'Não é possível mover pasta para si mesma.',
        'cannot_move_to_child' => 'Não é possível mover pasta para uma subpasta.',
        'name_already_exists' => 'Já existe uma pasta com este nome.',
    ],

    'forms' => [
        'create' => [
            'title' => 'Criar Nova Pasta',
            'name' => 'Nome da Pasta',
            'description' => 'Descrição (opcional)',
            'parent' => 'Pasta Pai (opcional)',
            'type' => 'Tipo',
            'create' => 'Criar Pasta',
        ],
        'edit' => [
            'title' => 'Editar Pasta',
            'name' => 'Nome da Pasta',
            'description' => 'Descrição',
            'parent' => 'Pasta Pai',
            'type' => 'Tipo',
            'update' => 'Atualizar Pasta',
        ],
        'move' => [
            'title' => 'Mover Pasta',
            'current_location' => 'Localização Atual',
            'new_location' => 'Nova Localização',
            'move' => 'Mover Pasta',
        ],
        'copy' => [
            'title' => 'Copiar Pasta',
            'source' => 'Pasta de Origem',
            'destination' => 'Destino',
            'copy_qr_codes' => 'Copiar QR Codes',
            'copy' => 'Copiar Pasta',
        ],
    ],

    'bulk_actions' => [
        'title' => 'Ações em Lote',
        'selected_items' => ':count item(s) selecionado(s)',
        'move_to' => 'Mover para',
        'copy_to' => 'Copiar para',
        'delete_selected' => 'Excluir Selecionados',
        'archive_selected' => 'Arquivar Selecionados',
        'restore_selected' => 'Restaurar Selecionados',
        'confirm_delete' => 'Tem certeza que deseja excluir os itens selecionados?',
        'confirm_archive' => 'Tem certeza que deseja arquivar os itens selecionados?',
        'confirm_restore' => 'Tem certeza que deseja restaurar os itens selecionados?',
    ],

    'filters' => [
        'title' => 'Filtros',
        'type' => 'Tipo',
        'status' => 'Status',
        'created_date' => 'Data de Criação',
        'all_types' => 'Todos os Tipos',
        'all_status' => 'Todos os Status',
        'apply' => 'Aplicar',
        'clear' => 'Limpar',
    ],

    'search' => [
        'title' => 'Buscar Pastas',
        'placeholder' => 'Digite o nome da pasta...',
        'no_results' => 'Nenhuma pasta encontrada.',
        'search_in' => 'Buscar em',
        'current_folder' => 'Pasta Atual',
        'all_folders' => 'Todas as Pastas',
    ],

    'sort' => [
        'title' => 'Ordenar por',
        'name' => 'Nome',
        'created_at' => 'Data de Criação',
        'updated_at' => 'Data de Atualização',
        'qr_codes_count' => 'Número de QR Codes',
        'asc' => 'Crescente',
        'desc' => 'Decrescente',
    ],

    'permissions' => [
        'title' => 'Permissões da Pasta',
        'view' => 'Visualizar',
        'create' => 'Criar',
        'edit' => 'Editar',
        'delete' => 'Excluir',
        'manage' => 'Gerenciar',
        'owner' => 'Proprietário',
        'team_member' => 'Membro da Equipe',
        'public' => 'Público',
    ],

    'sharing' => [
        'title' => 'Compartilhamento',
        'share_with_team' => 'Compartilhar com Equipe',
        'make_public' => 'Tornar Pública',
        'share_link' => 'Link de Compartilhamento',
        'copy_link' => 'Copiar Link',
        'link_copied' => 'Link copiado para a área de transferência!',
    ],

    'help' => [
        'title' => 'Ajuda com Pastas',
        'faq' => [
            'what_are_folders' => 'O que são pastas?',
            'how_to_organize' => 'Como organizar QR Codes em pastas?',
            'nested_folders' => 'Como criar subpastas?',
            'share_folders' => 'Como compartilhar pastas?',
            'bulk_actions' => 'Como usar ações em lote?',
        ],
    ],

    'errors' => [
        'name_required' => 'O nome da pasta é obrigatório.',
        'name_unique' => 'Já existe uma pasta com este nome.',
        'parent_exists' => 'A pasta pai deve existir.',
        'cannot_be_parent_of_self' => 'Uma pasta não pode ser pai de si mesma.',
        'circular_reference' => 'Referência circular detectada.',
        'permission_denied' => 'Permissão negada.',
        'folder_not_empty' => 'A pasta não está vazia.',
        'invalid_type' => 'Tipo de pasta inválido.',
        'invalid_status' => 'Status de pasta inválido.',
    ],
];
