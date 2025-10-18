<?php

use Illuminate\Support\Facades\Route;

// Rota de teste básica
Route::get('/', function () {
    return 'Laravel funcionando! - ' . now();
});

// Rota de teste com parâmetro
Route::get('/test', function () {
    return 'Teste funcionando! - ' . now();
});

// Rota de login simples
Route::get('/login', function () {
    return 'Login page - ' . now();
});

// Rota de dashboard simples
Route::get('/dashboard', function () {
    return 'Dashboard page - ' . now();
});

// Rota de QR Codes simples
Route::get('/qrcodes', function () {
    return 'QR Codes page - ' . now();
});

// Rota de criar QR Code simples
Route::get('/qrcodes/create', function () {
    return 'Criar QR Code page - ' . now();
});
