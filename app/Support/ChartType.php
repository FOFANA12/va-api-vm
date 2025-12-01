<?php

namespace App\Support;

class ChartType
{
    /**
     * List of document types with localized names.
     */
    private static array $types = [
        [
            'code' => 'LINE',
            'name' => [
                'fr' => 'Courbe',
                'en' => 'Line chart',
                'ar' => 'مخطط خطي',
            ],
        ],
        [
            'code' => 'BAR',
            'name' => [
                'fr' => 'Histogramme',
                'en' => 'Bar chart',
                'ar' => 'مخطط عمودي',
            ],
        ],
        [
            'code' => 'PIE',
            'name' => [
                'fr' => 'Camembert',
                'en' => 'Pie chart',
                'ar' => 'مخطط دائري',
            ],
        ],
        [
            'code' => 'GAUGE',
            'name' => [
                'fr' => 'Jauge',
                'en' => 'Gauge chart',
                'ar' => 'مقياس',
            ],
        ],
        [
            'code' => 'TABLE',
            'name' => [
                'fr' => 'Tableau',
                'en' => 'Table',
                'ar' => 'جدول',
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
