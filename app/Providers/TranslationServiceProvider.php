<?php

namespace App\Providers;

use App\Helpers\TranslationHelper;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('translation', function ($app) {
            return new TranslationHelper();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Registrar diretivas Blade personalizadas
        $this->registerBladeDirectives();
        
        // Configurar locale padrão
        $this->configureLocale();
    }

    /**
     * Registrar diretivas Blade personalizadas
     */
    protected function registerBladeDirectives(): void
    {
        // Diretiva @currency
        Blade::directive('currency', function ($expression) {
            return "<?php echo App\Helpers\TranslationHelper::currency($expression); ?>";
        });

        // Diretiva @number
        Blade::directive('number', function ($expression) {
            return "<?php echo App\Helpers\TranslationHelper::number($expression); ?>";
        });

        // Diretiva @date
        Blade::directive('date', function ($expression) {
            return "<?php echo App\Helpers\TranslationHelper::date($expression); ?>";
        });

        // Diretiva @datetime
        Blade::directive('datetime', function ($expression) {
            return "<?php echo App\Helpers\TranslationHelper::datetime($expression); ?>";
        });

        // Diretiva @timeAgo
        Blade::directive('timeAgo', function ($expression) {
            return "<?php echo App\Helpers\TranslationHelper::timeAgo($expression); ?>";
        });

        // Diretiva @rtl
        Blade::directive('rtl', function ($expression) {
            return "<?php if (App\Helpers\TranslationHelper::isRtl($expression)): ?>";
        });

        // Diretiva @endrtl
        Blade::directive('endrtl', function () {
            return "<?php endif; ?>";
        });

        // Diretiva @ltr
        Blade::directive('ltr', function ($expression) {
            return "<?php if (!App\Helpers\TranslationHelper::isRtl($expression)): ?>";
        });

        // Diretiva @endltr
        Blade::directive('endltr', function () {
            return "<?php endif; ?>";
        });
    }

    /**
     * Configurar locale padrão
     */
    protected function configureLocale(): void
    {
        // Definir locale padrão se não estiver definido
        if (!app()->getLocale()) {
            app()->setLocale(config('translation.default_locale', 'pt_BR'));
        }

        // Configurar timezone
        if (config('translation.timezone')) {
            date_default_timezone_set(config('translation.timezone'));
        }
    }
}
