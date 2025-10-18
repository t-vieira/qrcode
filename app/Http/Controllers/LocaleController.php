<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Helpers\TranslationHelper;

class LocaleController extends Controller
{
    /**
     * Alterar idioma
     */
    public function change(Request $request, string $locale)
    {
        // Verificar se o locale é válido
        if (!TranslationHelper::hasValidLocale($locale)) {
            return redirect()->back()->with('error', __('messages.error.invalid_locale'));
        }

        // Definir locale
        App::setLocale($locale);
        Session::put('locale', $locale);

        // Se usuário autenticado, salvar preferência
        if ($request->user()) {
            $request->user()->update(['locale' => $locale]);
        }

        return redirect()->back()->with('success', __('messages.success.locale_changed'));
    }

    /**
     * Obter locale atual
     */
    public function current()
    {
        return response()->json([
            'locale' => App::getLocale(),
            'available_locales' => TranslationHelper::getAvailableLocales(),
            'is_rtl' => TranslationHelper::isRtl(),
            'text_direction' => TranslationHelper::getTextDirection(),
        ]);
    }

    /**
     * Obter traduções para JavaScript
     */
    public function translations(Request $request)
    {
        $locale = $request->get('locale', App::getLocale());
        $files = $request->get('files', ['messages', 'validation', 'auth']);

        $translations = [];
        foreach ($files as $file) {
            $translations[$file] = TranslationHelper::getAll($file, $locale);
        }

        return response()->json($translations);
    }

    /**
     * Obter configurações de localização
     */
    public function config()
    {
        return response()->json([
            'locale' => App::getLocale(),
            'fallback_locale' => config('app.fallback_locale'),
            'available_locales' => TranslationHelper::getAvailableLocales(),
            'date_format' => config('translation.date_format'),
            'datetime_format' => config('translation.datetime_format'),
            'time_format' => config('translation.time_format'),
            'currency' => config('translation.currency'),
            'number_format' => config('translation.number_format'),
            'timezone' => config('translation.timezone'),
            'is_rtl' => TranslationHelper::isRtl(),
            'text_direction' => TranslationHelper::getTextDirection(),
        ]);
    }
}
