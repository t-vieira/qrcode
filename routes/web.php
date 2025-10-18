<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SubscriptionController;

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

// Redirecionamento de QR Codes (deve vir por último)
Route::get('/{shortCode}', [RedirectController::class, 'redirect'])
    ->where('shortCode', '[a-zA-Z0-9\-_]+')
    ->name('qr.redirect');

Route::get('/qr/text/{encodedContent}', [RedirectController::class, 'showText'])
    ->name('qr.text');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Webhooks (sem autenticação)
Route::post('/subscription/webhook', [SubscriptionController::class, 'webhook'])->name('subscription.webhook');
// Route::post('/whatsapp/webhook', [SupportController::class, 'webhook'])->name('whatsapp.webhook');

// Rotas autenticadas
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // QR Codes
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
    
    // Domínios customizados (comentado até implementar controller)
    // Route::resource('domains', CustomDomainController::class);
    // Route::post('/domains/{domain}/verify', [CustomDomainController::class, 'verify'])->name('domains.verify');
    // Route::post('/domains/{domain}/primary', [CustomDomainController::class, 'setPrimary'])->name('domains.primary');
    // Route::get('/domains/{domain}/instructions', [CustomDomainController::class, 'instructions'])->name('domains.instructions');
    
    // Equipes (comentado até implementar controller)
    // Route::resource('teams', TeamController::class);
    // Route::post('/teams/{team}/members', [TeamController::class, 'addMember'])->name('teams.add-member');
    // Route::delete('/teams/{team}/members/{user}', [TeamController::class, 'removeMember'])->name('teams.remove-member');
    // Route::put('/teams/{team}/members/{user}/permissions', [TeamController::class, 'updateMemberPermissions'])->name('teams.update-permissions');
    // Route::post('/teams/{team}/leave', [TeamController::class, 'leave'])->name('teams.leave');
    
    // Compartilhamento social (comentado até implementar controller)
    // Route::get('/qrcodes/{qrCode}/share/whatsapp', [SocialShareController::class, 'whatsapp'])->name('qrcodes.share.whatsapp');
    // Route::get('/qrcodes/{qrCode}/share/facebook', [SocialShareController::class, 'facebook'])->name('qrcodes.share.facebook');
    // Route::get('/qrcodes/{qrCode}/share/twitter', [SocialShareController::class, 'twitter'])->name('qrcodes.share.twitter');
    // Route::get('/qrcodes/{qrCode}/share/linkedin', [SocialShareController::class, 'linkedin'])->name('qrcodes.share.linkedin');
    // Route::get('/qrcodes/{qrCode}/share/email', [SocialShareController::class, 'email'])->name('qrcodes.share.email');
    // Route::get('/qrcodes/{qrCode}/share/link', [SocialShareController::class, 'generatePublicLink'])->name('qrcodes.share.link');
    // Route::get('/qrcodes/{qrCode}/og', [SocialShareController::class, 'openGraph'])->name('qrcodes.og');
    
    // Suporte (comentado até implementar controller)
    // Route::resource('support', SupportController::class)->only(['index', 'create', 'store', 'show']);
    // Route::post('/support/tickets/{ticket}/close', [SupportController::class, 'close'])->name('support.close');
    // Route::post('/support/tickets/{ticket}/reopen', [SupportController::class, 'reopen'])->name('support.reopen');
    // Route::post('/support/tickets/{ticket}/reply', [SupportController::class, 'reply'])->name('support.reply');
    // Route::get('/support/status', [SupportController::class, 'status'])->name('support.status');
    // Route::post('/support/test-message', [SupportController::class, 'testMessage'])->name('support.test-message');
    
    // Ajuda e Documentação (comentado até implementar controller)
    // Route::get('/help', [HelpController::class, 'index'])->name('help.index');
    // Route::get('/help/faq', [HelpController::class, 'faq'])->name('help.faq');
    // Route::get('/help/tutorials', [HelpController::class, 'tutorials'])->name('help.tutorials');
    // Route::get('/help/privacy', [HelpController::class, 'privacy'])->name('help.privacy');
    // Route::get('/help/terms', [HelpController::class, 'terms'])->name('help.terms');
    // Route::get('/help/contact', [HelpController::class, 'contact'])->name('help.contact');
    
    // LGPD - Privacidade de Dados (comentado até implementar controller)
    // Route::get('/privacy', [DataPrivacyController::class, 'index'])->name('privacy.index');
    // Route::post('/privacy/export', [DataPrivacyController::class, 'export'])->name('privacy.export');
    // Route::get('/privacy/download/{filename}', [DataPrivacyController::class, 'download'])->name('privacy.download');
    // Route::post('/privacy/request-deletion', [DataPrivacyController::class, 'requestDeletion'])->name('privacy.request-deletion');
    // Route::post('/privacy/cancel-deletion', [DataPrivacyController::class, 'cancelDeletion'])->name('privacy.cancel-deletion');
    // Route::get('/privacy/status', [DataPrivacyController::class, 'status'])->name('privacy.status');
    
    // Sistema de Idiomas (comentado até implementar controller)
    // Route::get('/locale/current', [LocaleController::class, 'current'])->name('locale.current');
    // Route::get('/locale/translations', [LocaleController::class, 'translations'])->name('locale.translations');
    // Route::get('/locale/config', [LocaleController::class, 'config'])->name('locale.config');
    // Route::post('/locale/change/{locale}', [LocaleController::class, 'change'])->name('locale.change');
});

// Rotas que requerem assinatura ativa
Route::middleware(['auth', 'verified', 'subscription'])->group(function () {
    // Funcionalidades avançadas aqui
});
