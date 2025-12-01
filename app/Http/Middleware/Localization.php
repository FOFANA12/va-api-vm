<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Localization
{
    /**
     * Handle an incoming request and apply locale settings.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = explode(',', env('SUPPORTED_LOCALES', 'en,fr,ar'));
        $defaultLocale = env('APP_LOCALE', 'en');

        $locale = $request->query('locale');
        if (! $locale) {
            $acceptLang = $request->header('Accept-Language');
            $locale = $this->normalizeLocale($acceptLang);
        }

        if ($locale && in_array($locale, $supportedLocales)) {
            $this->setLocale($locale);
        } else {
            $this->setLocale($defaultLocale);
        }

        return $next($request);
    }

    /**
     * Normalize locale string (e.g., "en-US" => "en")
     */
    protected function normalizeLocale(?string $locale): ?string
    {
        if (! $locale) {
            return null;
        }

        // Extract the primary language code from the header
        return strtolower(substr($locale, 0, 2));
    }

    /**
     * Set the locale globally for Laravel, Carbon and PHP
     */
    protected function setLocale(string $locale): void
    {
        app()->setLocale($locale);
        setlocale(LC_ALL, $locale);
        Carbon::setLocale($locale);
    }
}
