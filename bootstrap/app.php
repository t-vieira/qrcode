<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'subscription' => \App\Http\Middleware\CheckSubscription::class,
            'locale' => \App\Http\Middleware\SetLocale::class,
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
            'file.upload' => \App\Http\Middleware\ValidateFileUpload::class,
            'suspicious.activity' => \App\Http\Middleware\BlockSuspiciousActivity::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
        
        // Temporariamente desabilitar middlewares customizados para debug
        // $middleware->web(append: [
        //     \App\Http\Middleware\SetLocale::class,
        //     \App\Http\Middleware\SecurityHeaders::class,
        //     \App\Http\Middleware\BlockSuspiciousActivity::class,
        // ]);
        
        // $middleware->web(prepend: [
        //     \App\Http\Middleware\ValidateFileUpload::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
