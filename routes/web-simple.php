<?php

use Illuminate\Support\Facades\Route;

// Rota de teste básica
Route::get('/', function () {
    return 'Laravel funcionando!';
});

// Rota de teste com parâmetro
Route::get('/test', function () {
    return 'Teste funcionando!';
});

// Rota de teste de autenticação
Route::get('/test-auth', function () {
    return 'Auth test: ' . (auth()->check() ? 'User logged in: ' . auth()->user()->name : 'Not logged in');
})->middleware('auth');

// Rota de login simples
Route::get('/login', function () {
    return 'Login page';
});

// Rota de dashboard simples
Route::get('/dashboard', function () {
    return 'Dashboard page - User: ' . (auth()->check() ? auth()->user()->name : 'Not logged in');
})->middleware('auth');
