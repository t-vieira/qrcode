<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\SubscriptionController;
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

// Redirecionamento de QR Codes (deve vir antes das rotas autenticadas)
Route::get('/{shortCode}', [RedirectController::class, 'redirect'])
    ->where('shortCode', '[a-zA-Z0-9\-_]+')
    ->name('qr.redirect');

Route::get('/qr/text/{encodedContent}', [RedirectController::class, 'showText'])
    ->name('qr.text');

// Webhooks (sem autenticação)
Route::post('/subscription/webhook', [SubscriptionController::class, 'webhook'])->name('subscription.webhook');

// Rotas autenticadas
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // QR Codes CRUD
    Route::resource('qrcodes', QrCodeController::class);
    Route::get('/qrcodes/{qrCode}/download/{format?}', [QrCodeController::class, 'download'])
        ->name('qrcodes.download');
    Route::post('/qrcodes/preview', [QrCodeController::class, 'preview'])
        ->name('qrcodes.preview');
    
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
