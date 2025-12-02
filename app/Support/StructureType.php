<?php

namespace App\Support;

class StructureType
{
    /**
     * List of structure types with localized names.
     */
    private static array $types = [
        [
            'code' => 'STATE',
            'name' => [
                'fr' => 'State',
                'en' => 'State',
                'ar' => 'الدولة',
            ],
        ],
        [
            'code' => 'STRATEGIC',
            'name' => [
                'fr' => 'Stratégique',
                'en' => 'Strategic',
                'ar' => 'استراتيجي',
            ],
        ],
        [
            'code' => 'OPERATIONAL',
            'name' => [
                'fr' => 'Opérationnel',
                'en' => 'Operational',
                'ar' => 'تشغيلي',
            ],
        ],
        [
            'code' => 'VIRTUAL',
            'name' => [
                'fr' => 'Virtuel',
                'en' => 'Virtual',
                'ar' => 'افتراضي',
            ],
        ],
    ];

    /**
     * Get all structure types.
     */
    public static function all(): array
    {
        return self::$types;
    }

    /**
     * Get all structure type codes.
     */
    public static function codes(): array
    {
        return array_column(self::$types, 'code');
    }

    /**
     * Get the localized name of a structure type.
     * Falls back to French if the requested locale is not available.
     */
    public static function name(string $code, string $locale = 'fr'): ?string
    {
        foreach (self::$types as $type) {
            if ($type['code'] === strtoupper($code)) {
                return $type['name'][$locale] ?? $type['name']['fr'];
            }
        }

        return null;
    }

    /**
     * Get a structure type object containing code and localized label.
     */
    public static function get(string $code, string $locale = 'fr'): ?object
    {
        foreach (self::$types as $type) {
            if ($type['code'] === strtoupper($code)) {
                return (object) [
                    'code' => $type['code'],
                    'name' => $type['name'][$locale] ?? $type['name']['fr'],
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
