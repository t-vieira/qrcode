<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Linhas de Linguagem de Autenticação
    |--------------------------------------------------------------------------
    |
    | As seguintes linhas de linguagem são usadas durante a autenticação para
    | várias mensagens que precisamos exibir ao usuário. Você está livre para
    | modificar essas linhas de linguagem de acordo com os requisitos da sua
    | aplicação.
    |
    */

    'failed' => 'Essas credenciais não correspondem aos nossos registros.',
    'password' => 'A senha fornecida está incorreta.',
    'throttle' => 'Muitas tentativas de login. Tente novamente em :seconds segundos.',

    'login' => [
        'title' => 'Entrar',
        'email' => 'E-mail',
        'password' => 'Senha',
        'remember' => 'Lembrar de mim',
        'forgot' => 'Esqueceu sua senha?',
        'submit' => 'Entrar',
        'no_account' => 'Não tem uma conta?',
        'register' => 'Cadastre-se',
    ],

    'register' => [
        'title' => 'Cadastrar',
        'name' => 'Nome completo',
        'email' => 'E-mail',
        'password' => 'Senha',
        'password_confirmation' => 'Confirmar senha',
        'terms' => 'Aceito os :terms e :privacy',
        'terms_link' => 'Termos de Uso',
        'privacy_link' => 'Política de Privacidade',
        'submit' => 'Cadastrar',
        'has_account' => 'Já tem uma conta?',
        'login' => 'Faça login',
    ],

    'verify' => [
        'title' => 'Verificar E-mail',
        'message' => 'Antes de continuar, verifique seu e-mail para um link de verificação.',
        'resend' => 'Reenviar e-mail de verificação',
        'sent' => 'Um novo link de verificação foi enviado para seu endereço de e-mail.',
    ],

    'passwords' => [
        'email' => [
            'title' => 'Redefinir Senha',
            'email' => 'E-mail',
            'submit' => 'Enviar Link de Redefinição',
            'message' => 'Enviaremos um link de redefinição de senha para seu e-mail.',
        ],
        'reset' => [
            'title' => 'Redefinir Senha',
            'email' => 'E-mail',
            'password' => 'Nova senha',
            'password_confirmation' => 'Confirmar nova senha',
            'submit' => 'Redefinir Senha',
        ],
    ],

    'logout' => [
        'title' => 'Sair',
        'confirm' => 'Tem certeza que deseja sair?',
    ],
];
