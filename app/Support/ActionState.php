<?php

namespace App\Support;

class ActionState
{
    /**
     * List of available statuses with their localized names and display colors.
     */
    private static array $statuses = [
        [
            'code' => 'none',
            'color' => '#6c757d', // gris neutre
            'name' => [
                'fr' => 'Néant',
                'en' => 'None',
                'ar' => 'لا شيء',
            ],
        ],
        [
            'code' => 'on_track',
            'color' => '#28a745', // vert standard succès
            'name' => [
                'fr' => 'En bonne voie',
                'en' => 'On track',
                'ar' => 'على الطريق الصحيح',
            ],
        ],
        [
            'code' => 'delayed',
            'color' => '#fd7e14', // orange bien saturé
            'name' => [
                'fr' => 'En retard',
                'en' => 'Delayed',
                'ar' => 'متأخر',
            ],
        ],
        [
            'code' => 'bad_track',
            'color' => '#dc3545', // rouge vif (danger)
            'name' => [
                'fr' => 'En mauvaise voie',
                'en' => 'Off track',
                'ar' => 'في طريق سيء',
            ],
        ],
        [
            'code' => 'achieved',
            'color' => '#007bff', // bleu clair lisible
            'name' => [
                'fr' => 'Réalisé',
                'en' => 'Achieved',
                'ar' => 'محقق',
            ], //-
        ],
    ];

    /**
     * Get all statuses as an array.
     */
    public static function all(): array
    {
        return self::$statuses;
    }

    /**
     * Get only the list of status codes.
     */
    public static function codes(): array
    {
        return array_column(self::$statuses, 'code');
    }

    /**
     * Get the localized name of a given status code.
     * Defaults to French if the requested locale is not available.
     */
    public static function name(string $code, string $locale = 'fr'): ?string
    {
        foreach (self::$statuses as $status) {
            if ($status['code'] === $code) {
                return $status['name'][$locale] ?? $status['name']['fr'];
            }
        }

        return null;
    }

    /**
     * Get a specific status as an object with code, label and color.
     */
    public static function get(string $code, string $locale = 'fr'): ?object
    {
        foreach (self::$statuses as $status) {
            if ($status['code'] === $code) {
                return (object) [
                    'code' => $status['code'],
                    'label' => $status['name'][$locale] ?? $status['name']['fr'],
                    'color' => $status['color'],
                ];
            }
        }

        return null;
    }
}
