<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Rota de teste
Route::get('/', function () {
    return 'Laravel funcionando! - ' . now();
});

// Autenticação
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Dashboard simples (sem controller temporariamente)
Route::get('/dashboard', function () {
    return 'Dashboard funcionando! - ' . now();
})->name('dashboard');

// QR Codes (rotas básicas necessárias para o dashboard)
Route::middleware(['auth'])->group(function () {
    Route::resource('qrcodes', QrCodeController::class);
    Route::get('/qrcodes/{qrCode}/download/{format?}', [QrCodeController::class, 'download'])
        ->name('qrcodes.download');
    Route::get('/qrcodes/{qrCode}/scans', [QrCodeController::class, 'scans'])
        ->name('qrcodes.scans');
});
