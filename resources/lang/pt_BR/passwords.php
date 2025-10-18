<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Linhas de Linguagem de Redefinição de Senha
    |--------------------------------------------------------------------------
    |
    | As seguintes linhas de linguagem são as linhas padrão que correspondem
    | às razões fornecidas pelo corretor de senhas para uma tentativa de
    | atualização de senha que falhou, como para um token inválido ou
    | nova senha inválida.
    |
    */

    'reset' => 'Sua senha foi redefinida!',
    'sent' => 'Enviamos por e-mail o link para redefinir sua senha!',
    'throttled' => 'Por favor, aguarde antes de tentar novamente.',
    'token' => 'Este token de redefinição de senha é inválido.',
    'user' => "Não conseguimos encontrar um usuário com esse endereço de e-mail.",

    'email' => [
        'subject' => 'Redefinir Senha - QR Code SaaS',
        'greeting' => 'Olá!',
        'line1' => 'Você está recebendo este e-mail porque recebemos uma solicitação de redefinição de senha para sua conta.',
        'line2' => 'Clique no botão abaixo para redefinir sua senha:',
        'action' => 'Redefinir Senha',
        'line3' => 'Se você não solicitou uma redefinição de senha, nenhuma ação adicional é necessária.',
        'salutation' => 'Atenciosamente,',
        'team' => 'Equipe QR Code SaaS',
    ],
];
