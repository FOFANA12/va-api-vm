<?php

namespace App\Support;

class TaskPriority
{
    /**
     * Supported task priorities with localized names.
     */
    private static array $priorities = [
        [
            'code' => 'high',
            'level' => 3,
            'color' => '#F44336', // rouge
            'name' => [
                'fr' => 'Élevée',
                'en' => 'High',
                'ar' => 'مرتفعة',
            ],
        ],
        [
            'code' => 'medium',
            'level' => 2,
            'color' => '#FFC107', // jaune
            'name' => [
                'fr' => 'Moyenne',
                'en' => 'Medium',
                'ar' => 'متوسطة',
            ],
        ],
        [
            'code' => 'low',
            'level' => 1,
            'color' => '#03A9F4', // bleu clair
            'name' => [
                'fr' => 'Faible',
                'en' => 'Low',
                'ar' => 'منخفضة',
            ],
        ],
    ];

    /**
     * Return all priorities as an array.
     */
    public static function all(): array
    {
        return self::$priorities;
    }

    /**
     * Return only the priority codes.
     */
    public static function codes(): array
    {
        return array_column(self::$priorities, 'code');
    }

    /**
     * Return the localized label of a given priority code.
     * Defaults to French if the requested locale is not available.
     */
    public static function name(string $code, string $locale = 'fr'): ?string
    {
        foreach (self::$priorities as $priority) {
            if ($priority['code'] === $code) {
                return $priority['name'][$locale] ?? $priority['name']['fr'];
            }
        }

        return null;
    }

    /**
     * Return a specific priority as an object with code, label, level and color.
     */
    public static function get(string $code, string $locale = 'fr'): ?object
    {
        foreach (self::$priorities as $priority) {
            if ($priority['code'] === $code) {
                return (object) [
                    'code' => $priority['code'],
                    'label' => $priority['name'][$locale] ?? $priority['name']['fr'],
                    'level' => $priority['level'],
                    'color' => $priority['color'],
                ];
            }
        }

        return null;
    }
}
