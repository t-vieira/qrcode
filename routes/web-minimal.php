<?php

use Illuminate\Support\Facades\Route;

// Rota de teste
Route::get('/', function () {
    return 'Laravel funcionando! - ' . now();
});

// Dashboard simples
Route::get('/dashboard', function () {
    return 'Dashboard funcionando! - ' . now();
})->name('dashboard');
