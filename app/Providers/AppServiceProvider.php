<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configurar Carbon para português brasileiro
        Carbon::setLocale('pt_BR');
        
        // Configurar timezone para São Paulo
        date_default_timezone_set('America/Sao_Paulo');
    }
}
