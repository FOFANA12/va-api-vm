<?php

namespace App\Support;

class GenerateDocumentTypes
{
    /**
     * List of document types with localized names.
     */
    private static array $types = [
        [
            'code' => 'paa',
            'name' => [
                'fr' => 'Plan d\'achat annuel (PAA)',
                'en' => 'Annual Procurement Plan (APP)',
                'ar' => 'الخطة السنوية للمشتريات (PAA)',
            ],
        ],
        [
            'code' => 'ppm',
            'name' => [
                'fr' => 'Plan de Passation des Marchés (PPM)',
                'en' => 'Public Procurement Plan (PPM)',
                'ar' => 'خطة إبرام الصفقات العمومية (PPM)',
            ],
        ],
        [
            'code' => 'autre',
            'name' => [
                'fr' => 'Autre',
                'en' => 'Other',
                'ar' => 'أخرى',
            ],
        ],
    ];

    /**
     * Get all document types.
     */
    public static function all(): array
    {
        return self::$types;
    }

    /**
     * Get all document type codes.
     */
    public static function codes(): array
    {
        return array_column(self::$types, 'code');
    }

    /**
     * Get the localized name of a document type.
     * Falls back to French if the requested locale is not available.
     */
    public static function name(string $code, string $locale = 'fr'): ?string
    {
        foreach (self::$types as $type) {
            if ($type['code'] === $code) {
                return $type['name'][$locale] ?? $type['name']['fr'];
            }
        }

        return null;
    }

    /**
     * Get a document type object with code and localized label.
     */
    public static function get(string $code, string $locale = 'fr'): ?object
    {
        foreach (self::$types as $type) {
            if ($type['code'] === $code) {
                return (object) [
                    'code' => $type['code'],
                    'label' => $type['name'][$locale] ?? $type['name']['fr'],
                ];
            }
        }

        return null;
    }

    /**
     * Get a formatted array for select components: [value, label].
     */
    public static function forSelect(string $locale = 'fr'): array
    {
        return array_map(function ($type) use ($locale) {
            return [
                'value' => $type['code'],
                'label' => $type['name'][$locale] ?? $type['name']['fr'],
            ];
        }, self::$types);
    }
}
