<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\SubscriptionController;
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

// Redirecionamento de QR Codes (sem autenticação)
Route::get('/r/{shortCode}', [RedirectController::class, 'redirect'])->name('qr.redirect');

// Webhooks (sem autenticação)
Route::post('/subscription/webhook', [SubscriptionController::class, 'webhook'])->name('subscription.webhook');

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
    
    // Assinaturas
    Route::get('/subscription/upgrade', [SubscriptionController::class, 'upgrade'])->name('subscription.upgrade');
    Route::post('/subscription/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    Route::post('/subscription/pix', [SubscriptionController::class, 'createPixPayment'])->name('subscription.pix');
    Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
    Route::get('/subscription/status', [SubscriptionController::class, 'status'])->name('subscription.status');
    Route::get('/subscription/success', [SubscriptionController::class, 'success'])->name('subscription.success');
    Route::get('/subscription/failure', [SubscriptionController::class, 'failure'])->name('subscription.failure');
    Route::get('/subscription/pending', [SubscriptionController::class, 'pending'])->name('subscription.pending');
});

// Rota de teste autenticada
Route::get('/test-auth', function () {
    return 'Auth test: ' . (auth()->check() ? 'User logged in: ' . auth()->user()->name : 'Not logged in');
})->middleware('auth');
