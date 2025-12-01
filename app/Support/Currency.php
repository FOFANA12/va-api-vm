<?php

namespace App\Support;

class Currency
{
    /**
     * List of supported currencies.
     * Extend this easily when adding multi-currency support later.
     */
    private static array $currencies = [
        [
            'code' => 'MRU',
            'name' => [
                'fr' => 'Ouguiya mauritanienne',
                'en' => 'Mauritanian Ouguiya',
            ],
            'symbol' => 'UM',
            'is_default' => true,
        ],
        [
            'code' => 'USD',
            'name' => [
                'fr' => 'Dollar américain',
                'en' => 'US Dollar',
            ],
            'symbol' => '$',
            'is_default' => false,
        ],
        [
            'code' => 'EUR',
            'name' => [
                'fr' => 'Euro',
                'en' => 'Euro',
            ],
            'symbol' => '€',
            'is_default' => false,
        ],
    ];

    /**
     * Return all supported currencies.
     */
    public static function all(): array
    {
        return self::$currencies;
    }

    /**
     * Get only the currency codes (e.g., ['MRU', 'USD', 'EUR']).
     */
    public static function codes(): array
    {
        return array_column(self::$currencies, 'code');
    }

    /**
     * Get the default currency (the one marked is_default = true).
     */
    public static function getDefault(string $locale = 'fr'): array
    {
        $default = collect(self::$currencies)->firstWhere('is_default', true)
            ?? self::$currencies[0];

        return [
            'code' => $default['code'],
            'name' => $default['name'][$locale] ?? $default['name']['en'],
            'symbol' => $default['symbol'],
            'is_default' => true,
        ];
    }

    /**
     * Get a currency object by its code (case-insensitive).
     */
    public static function getObject(string $code, string $locale = 'fr'): ?array
    {
        foreach (self::$currencies as $currency) {
            if (strcasecmp($currency['code'], $code) === 0) {
                return [
                    'code' => $currency['code'],
                    'name' => $currency['name'][$locale] ?? $currency['name']['en'],
                    'symbol' => $currency['symbol'],
                    'is_default' => $currency['is_default'],
                ];
            }
        }

        return null;
    }

    /**
     * Get the symbol for a given currency code.
     */
    public static function symbol(string $code): ?string
    {
        $currency = self::getObject($code);
        return $currency['symbol'] ?? null;
    }

    /**
     * Get the localized name for a given currency code.
     */
    public static function name(string $code, string $locale = 'fr'): ?string
    {
        $currency = self::getObject($code, $locale);
        return $currency['name'] ?? null;
    }
}
