<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Rota de teste básica
Route::get('/', function () {
    return 'Laravel funcionando! - ' . now();
});

// Rota de teste
Route::get('/test', function () {
    return 'Teste funcionando! - ' . now();
});

// Páginas públicas de ajuda
Route::get('/help/terms', function () {
    return 'Termos de uso - ' . now();
})->name('help.terms');

Route::get('/help/privacy', function () {
    return 'Política de privacidade - ' . now();
})->name('help.privacy');

// Autenticação
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rotas autenticadas
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // QR Codes CRUD
    Route::get('/qrcodes', [QrCodeController::class, 'index'])->name('qrcodes.index');
    Route::get('/qrcodes/create', [QrCodeController::class, 'create'])->name('qrcodes.create');
    Route::post('/qrcodes', [QrCodeController::class, 'store'])->name('qrcodes.store');
    Route::get('/qrcodes/{qrCode}', [QrCodeController::class, 'show'])->name('qrcodes.show');
    Route::get('/qrcodes/{qrCode}/edit', [QrCodeController::class, 'edit'])->name('qrcodes.edit');
    Route::put('/qrcodes/{qrCode}', [QrCodeController::class, 'update'])->name('qrcodes.update');
    Route::delete('/qrcodes/{qrCode}', [QrCodeController::class, 'destroy'])->name('qrcodes.destroy');
    Route::get('/qrcodes/{qrCode}/download/{format?}', [QrCodeController::class, 'download'])->name('qrcodes.download');
    Route::post('/qrcodes/preview', [QrCodeController::class, 'preview'])->name('qrcodes.preview');
});

// Rota de teste autenticada
Route::get('/test-auth', function () {
    return 'Auth test: ' . (auth()->check() ? 'User logged in: ' . auth()->user()->name : 'Not logged in');
})->middleware('auth');
