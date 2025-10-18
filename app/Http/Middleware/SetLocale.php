<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Helpers\TranslationHelper;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->getLocale($request);
        
        // Definir locale
        App::setLocale($locale);
        
        // Armazenar na sessão para futuras requisições
        Session::put('locale', $locale);
        
        return $next($request);
    }

    /**
     * Determinar o locale a ser usado
     */
    protected function getLocale(Request $request): string
    {
        // 1. Verificar parâmetro da URL
        if ($request->has('lang')) {
            $locale = $request->get('lang');
            if ($this->isValidLocale($locale)) {
                return $locale;
            }
        }

        // 2. Verificar sessão
        if (Session::has('locale')) {
            $locale = Session::get('locale');
            if ($this->isValidLocale($locale)) {
                return $locale;
            }
        }

        // 3. Verificar preferência do usuário autenticado
        if ($request->user() && $request->user()->locale) {
            $locale = $request->user()->locale;
            if ($this->isValidLocale($locale)) {
                return $locale;
            }
        }

        // 4. Verificar header Accept-Language
        $acceptLanguage = $request->header('Accept-Language');
        if ($acceptLanguage) {
            $locale = $this->parseAcceptLanguage($acceptLanguage);
            if ($locale) {
                return $locale;
            }
        }

        // 5. Usar locale padrão
        return config('translation.default_locale', 'pt_BR');
    }

    /**
     * Verificar se o locale é válido
     */
    protected function isValidLocale(string $locale): bool
    {
        $availableLocales = array_keys(config('translation.available_locales', []));
        return in_array($locale, $availableLocales);
    }

    /**
     * Parsear header Accept-Language
     */
    protected function parseAcceptLanguage(string $acceptLanguage): ?string
    {
        $locales = [];
        $availableLocales = array_keys(config('translation.available_locales', []));
        
        // Parsear header Accept-Language
        $parts = explode(',', $acceptLanguage);
        foreach ($parts as $part) {
            $part = trim($part);
            if (strpos($part, ';') !== false) {
                list($locale, $quality) = explode(';', $part, 2);
                $quality = (float) str_replace('q=', '', $quality);
            } else {
                $locale = $part;
                $quality = 1.0;
            }
            
            $locale = trim($locale);
            if ($this->isValidLocale($locale)) {
                $locales[$locale] = $quality;
            }
        }
        
        // Ordenar por qualidade
        arsort($locales);
        
        // Retornar o primeiro locale válido
        return !empty($locales) ? array_key_first($locales) : null;
    }
}
