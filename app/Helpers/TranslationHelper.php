<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class TranslationHelper
{
    /**
     * Obter tradução com fallback
     */
    public static function get(string $key, array $replace = [], ?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        
        // Tentar obter a tradução no locale solicitado
        $translation = __($key, $replace, $locale);
        
        // Se não encontrou e não é o locale de fallback, tentar o fallback
        if ($translation === $key && $locale !== config('app.fallback_locale')) {
            $translation = __($key, $replace, config('app.fallback_locale'));
        }
        
        return $translation;
    }

    /**
     * Obter tradução com pluralização
     */
    public static function choice(string $key, int $number, array $replace = [], ?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        
        return trans_choice($key, $number, array_merge($replace, ['count' => $number]), $locale);
    }

    /**
     * Formatar moeda
     */
    public static function currency(float $amount, ?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        $config = config('translation.currency');
        
        $formatted = number_format(
            $amount,
            $config['decimals'],
            $config['decimal_separator'],
            $config['thousands_separator']
        );
        
        if ($config['position'] === 'before') {
            return $config['symbol'] . ' ' . $formatted;
        }
        
        return $formatted . ' ' . $config['symbol'];
    }

    /**
     * Formatar número
     */
    public static function number(float $number, int $decimals = null, ?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        $config = config('translation.number_format');
        
        $decimals = $decimals ?? $config['decimals'];
        
        return number_format(
            $number,
            $decimals,
            $config['decimal_separator'],
            $config['thousands_separator']
        );
    }

    /**
     * Formatar data
     */
    public static function date(\DateTime|string $date, ?string $format = null, ?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        $format = $format ?? config('translation.date_format');
        
        if (is_string($date)) {
            $date = new \DateTime($date);
        }
        
        return $date->format($format);
    }

    /**
     * Formatar data e hora
     */
    public static function datetime(\DateTime|string $datetime, ?string $format = null, ?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        $format = $format ?? config('translation.datetime_format');
        
        if (is_string($datetime)) {
            $datetime = new \DateTime($datetime);
        }
        
        return $datetime->format($format);
    }

    /**
     * Formatar tempo relativo
     */
    public static function timeAgo(\DateTime|string $datetime, ?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        
        if (is_string($datetime)) {
            $datetime = new \DateTime($datetime);
        }
        
        $now = new \DateTime();
        $diff = $now->diff($datetime);
        
        if ($diff->y > 0) {
            return self::choice('messages.time.years_ago', $diff->y, [], $locale);
        } elseif ($diff->m > 0) {
            return self::choice('messages.time.months_ago', $diff->m, [], $locale);
        } elseif ($diff->d > 0) {
            return self::choice('messages.time.days_ago', $diff->d, [], $locale);
        } elseif ($diff->h > 0) {
            return self::choice('messages.time.hours_ago', $diff->h, [], $locale);
        } elseif ($diff->i > 0) {
            return self::choice('messages.time.minutes_ago', $diff->i, [], $locale);
        } else {
            return self::get('messages.time.just_now', [], $locale);
        }
    }

    /**
     * Obter locale atual
     */
    public static function getCurrentLocale(): string
    {
        return App::getLocale();
    }

    /**
     * Definir locale
     */
    public static function setLocale(string $locale): void
    {
        App::setLocale($locale);
    }

    /**
     * Obter locales disponíveis
     */
    public static function getAvailableLocales(): array
    {
        return config('translation.available_locales', []);
    }

    /**
     * Verificar se locale é RTL
     */
    public static function isRtl(?string $locale = null): bool
    {
        $locale = $locale ?? App::getLocale();
        $rtlLocales = config('translation.rtl_locales', []);
        
        return in_array($locale, $rtlLocales);
    }

    /**
     * Obter direção do texto
     */
    public static function getTextDirection(?string $locale = null): string
    {
        return self::isRtl($locale) ? 'rtl' : 'ltr';
    }

    /**
     * Limpar cache de traduções
     */
    public static function clearCache(): void
    {
        Cache::forget('translations');
    }

    /**
     * Carregar traduções do arquivo
     */
    public static function loadFromFile(string $file, ?string $locale = null): array
    {
        $locale = $locale ?? App::getLocale();
        $path = resource_path("lang/{$locale}/{$file}.php");
        
        if (File::exists($path)) {
            return require $path;
        }
        
        // Fallback para o locale padrão
        $fallbackPath = resource_path("lang/" . config('app.fallback_locale') . "/{$file}.php");
        if (File::exists($fallbackPath)) {
            return require $fallbackPath;
        }
        
        return [];
    }

    /**
     * Verificar se chave de tradução existe
     */
    public static function has(string $key, ?string $locale = null): bool
    {
        $locale = $locale ?? App::getLocale();
        $translation = __($key, [], $locale);
        
        return $translation !== $key;
    }

    /**
     * Verificar se locale é válido
     */
    public static function hasValidLocale(string $locale): bool
    {
        $availableLocales = array_keys(config('translation.available_locales', []));
        return in_array($locale, $availableLocales);
    }

    /**
     * Obter todas as traduções de um arquivo
     */
    public static function getAll(string $file, ?string $locale = null): array
    {
        $locale = $locale ?? App::getLocale();
        
        return Cache::remember("translations.{$locale}.{$file}", 3600, function () use ($file, $locale) {
            return self::loadFromFile($file, $locale);
        });
    }

    /**
     * Interpolar variáveis em string
     */
    public static function interpolate(string $string, array $variables = []): string
    {
        foreach ($variables as $key => $value) {
            $string = str_replace(":{$key}", $value, $string);
        }
        
        return $string;
    }
}
