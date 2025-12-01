<?php

namespace App\Support;

class StrategicObjectiveStatus
{
    /**
     * List of available statuses with their localized names and display colors.
     */
    private static array $statuses = [
        [
            'code' => 'draft',
            'color' => '#9e9e9e',
            'name' => [
                'fr' => 'Brouillon',
                'en' => 'Draft',
                'ar' => 'مسودة',
            ],
        ],
        [
            'code' => 'declared',
            'color' => '#42a5f5', // Bleu léger
            'name' => [
                'fr' => 'Déclaré',
                'en' => 'Declared',
                'ar' => 'مصرح',
            ],
        ],
        [
            'code' => 'engaged',
            'color' => '#ff9800', // Orange
            'name' => [
                'fr' => 'Engagé',
                'en' => 'Engaged',
                'ar' => 'مشارك',
            ],
        ],
        [
            'code' => 'closed',
            'color' => '#4caf50', // Vert
            'name' => [
                'fr' => 'Clôturé',
                'en' => 'Closed',
                'ar' => 'مختوم',
            ],
        ],
        [
            'code' => 'stopped',
            'color' => '#f44336', // Rouge
            'name' => [
                'fr' => 'En arrêt',
                'en' => 'Stopped',
                'ar' => 'متوقف',
            ],
        ],
    ];

    /**
     * Allowed transitions between statuses.
     */
    private static array $transitions = [
        'draft'     => ['declared'],             // Brouillon → Déclaré
        'declared'  => ['engaged'],              // Déclaré → Engagé
        'engaged'   => ['closed', 'stopped'],    // Engagé → Clôturé ou En arrêt
        'stopped'   => ['engaged', 'closed'],    // En arrêt → Engagé ou Clôturé
        'closed'    => [],                       // Clôturé = final
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

    /**
     * Get possible next statuses for a given status.
     */
    public static function next(string $code): array
    {
        return self::$transitions[$code] ?? [];
    }

    /**
     * Check if transition is allowed.
     */
    public static function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::$transitions[$from] ?? [], true);
    }
}
