<?php

namespace App\Support;

class FrequencyUnit
{
    /**
     * Supported frequency units with localized names.
     */
    private static array $units = [
        ['code' => 'days', 'name' => ['fr' => 'Jour',    'ar' => 'يوم',     'en' => 'Day']],
        ['code' => 'weeks', 'name' => ['fr' => 'Semaine', 'ar' => 'أسبوع',  'en' => 'Week']],
        ['code' => 'months', 'name' => ['fr' => 'Mois',   'ar' => 'شهر',    'en' => 'Month']],
        ['code' => 'years', 'name' => ['fr' => 'An',     'ar' => 'سنة',    'en' => 'Year']],
    ];

    /**
     * Return all frequency units.
     */
    public static function all(): array
    {
        return self::$units;
    }

    /**
     * Return only the unit codes.
     */
    public static function codes(): array
    {
        return array_column(self::$units, 'code');
    }

    /**
     * Return the localized name of a given unit code.
     * Defaults to French if the requested locale is not available.
     */
    public static function name(string $code, string $locale = 'fr'): ?string
    {
        foreach (self::$units as $unit) {
            if ($unit['code'] === strtolower($code)) {
                return $unit['name'][$locale] ?? $unit['name']['fr'] ?? null;
            }
        }

        return null;
    }

    /**
     * Return a specific unit as an object with code and localized label.
     */
    public static function get(string $code, string $locale = 'fr'): ?object
    {
        foreach (self::$units as $unit) {
            if ($unit['code'] === strtolower($code)) {
                return (object) [
                    'code' => $unit['code'],
                    'label' => $unit['name'][$locale] ?? $unit['name']['fr'],
                ];
            }
        }

        return null;
    }
}
