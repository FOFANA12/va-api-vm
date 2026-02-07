<?php

namespace App\Support;

class ElementaryLevelState
{
    /**
     * List of possible states with localized names and display colors.
     */
    private static array $states = [
        [
            'code' => 'none',
            'color' => '#6c757d', // gray neutral
            'name' => [
                'fr' => 'Néant',
                'en' => 'None',
                'ar' => 'لا شيء',
            ],
        ],
        [
            'code' => 'on_track',
            'color' => '#22c55e', // green
            'name' => [
                'fr' => 'En bonne voie',
                'en' => 'On track',
                'ar' => 'على المسار الصحيح',
            ],
        ],
        [
            'code' => 'delayed',
            'color' => '#facc15', // yellow
            'name' => [
                'fr' => 'En retard',
                'en' => 'Delayed',
                'ar' => 'متأخر',
            ],
        ],
        [
            'code' => 'off_track',
            'color' => '#ef4444', // red
            'name' => [
                'fr' => 'En mauvaise voie',
                'en' => 'Off track',
                'ar' => 'في وضع سيء',
            ],
        ],
        [
            'code' => 'achieved',
            'color' => '#3b82f6', // blue
            'name' => [
                'fr' => 'Réalisé',
                'en' => 'Achieved',
                'ar' => 'منجز',
            ],
        ],
    ];


    /**
     * Allowed strategic states depending on elementary level status.
     */
    private static array $allowedByStatus = [
        'preparation' => [
            'none',
        ],

        'engaged' => [
            'on_track',
            'delayed',
            'off_track',
            'achieved',
        ],

        'closed' => [
            'on_track',
            'delayed',
            'off_track',
            'achieved',
        ],

        'stopped' => [
            'on_track',
            'delayed',
            'off_track',
        ],
    ];

    /**
     * Get the full list of states.
     */
    public static function all(): array
    {
        return self::$states;
    }

    /**
     * Get only the list of state codes.
     */
    public static function codes(): array
    {
        return array_column(self::$states, 'code');
    }

    /**
     * Get the localized name of a given state code.
     */
    public static function name(string $code, string $locale = 'fr'): ?string
    {
        foreach (self::$states as $state) {
            if ($state['code'] === $code) {
                return $state['name'][$locale] ?? $state['name']['fr'];
            }
        }

        return null;
    }

    /**
     * Get a specific state as an object with code, label, and color.
     */
    public static function get(string $code, string $locale = 'fr'): ?object
    {
        foreach (self::$states as $state) {
            if ($state['code'] === $code) {
                return (object) [
                    'code' => $state['code'],
                    'label' => $state['name'][$locale] ?? $state['name']['fr'],
                    'color' => $state['color'],
                ];
            }
        }

        return null;
    }

    /**
     * Get allowed states for a given status.
     */
    public static function allowedStatesForStatus(string $status, string $locale = 'fr'): array
    {
        $allowedCodes = self::$allowedByStatus[$status] ?? [];

        return array_values(array_map(
            fn($state) => (object) [
                'code' => $state['code'],
                'label' => $state['name'][$locale] ?? $state['name']['fr'],
                'color' => $state['color'],
            ],
            array_filter(
                self::$states,
                fn($state) => in_array($state['code'], $allowedCodes, true)
            )
        ));
    }

    /**
     * Check if a state is allowed for a given status.
     */
    public static function isAllowedForStatus(string $status, string $state): bool
    {
        return in_array($state, self::$allowedByStatus[$status] ?? [], true);
    }
}
