<?php

namespace App\Support;

class PriorityLevel
{
    /**
     * Supported priorities with their localized names.
     */
    private static array $priorities = [
        [
            'code' => 'very_low',
            'level' => 1,
            'color' => '#9e9e9e', // gris
            'name' => ['fr' => 'Très faible', 'en' => 'Very Low', 'ar' => 'منخفض جدًا'],
        ],
        [
            'code' => 'low',
            'level' => 2,
            'color' => '#03a9f4', // bleu clair
            'name' => ['fr' => 'Faible', 'en' => 'Low', 'ar' => 'منخفض'],
        ],
        [
            'code' => 'medium',
            'level' => 3,
            'color' => '#ffc107', // orange
            'name' => ['fr' => 'Moyenne', 'en' => 'Medium', 'ar' => 'متوسط'],
        ],
        [
            'code' => 'high',
            'level' => 4,
            'color' => '#ff9800', // orange foncé
            'name' => ['fr' => 'Élevée', 'en' => 'High', 'ar' => 'مرتفعة'],
        ],
        [
            'code' => 'very_high',
            'level' => 5,
            'color' => '#f44336', // rouge
            'name' => ['fr' => 'Très élevée', 'en' => 'Very High', 'ar' => 'مرتفعة جدًا'],
        ],
        [
            'code' => 'critical',
            'level' => 6,
            'color' => '#b71c1c', // rouge très foncé
            'name' => ['fr' => 'Critique', 'en' => 'Critical', 'ar' => 'حرج'],
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
                return $priority['name'][$locale] ?? $priority['name']['fr'] ?? null;
            }
        }

        return null;
    }

    /**
     * Return a specific priority as an object with code and localized label.
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
