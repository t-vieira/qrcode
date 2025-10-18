<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Rotas públicas
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Rota de teste
Route::get('/test-route', function () {
    return 'Test route is working!';
});

// Páginas públicas de ajuda
Route::get('/help/terms', function () {
    return view('help.terms');
})->name('help.terms');

Route::get('/help/privacy', function () {
    return view('help.privacy');
})->name('help.privacy');

// Autenticação (deve vir antes da rota catch-all)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rotas autenticadas
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Rota de teste autenticada
Route::get('/test-auth', function () {
    return 'Auth test: ' . (auth()->check() ? 'User logged in: ' . auth()->user()->name : 'Not logged in');
})->middleware('auth');
