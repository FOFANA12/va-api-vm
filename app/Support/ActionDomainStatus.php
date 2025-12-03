<?php

namespace App\Support;

class ActionDomainStatus
{
    /**
     * List of available statuses with their localized names and display colors.
     */
    private static array $statuses = [
        [
            'code' => 'preparation',
            'color' => '#fde68a', // warning color
            'name' => [
                'fr' => 'En préparation',
                'en' => 'In preparation',
                'ar' => 'قيد التحضير',
            ],
        ],
        [
            'code' => 'engaged',
            'color' => '#93c5fd', // bleu clair
            'name' => [
                'fr' => 'Engagé',
                'en' => 'Engaged',
                'ar' => 'منخرط',
            ],
        ],
        [
            'code' => 'closed',
            'color' => '#86efac', // vert clair
            'name' => [
                'fr' => 'Clôturé',
                'en' => 'Closed',
                'ar' => 'مغلق',
            ],
        ],
        [
            'code' => 'stopped',
            'color' => '#fca5a5', // rouge clair
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
        'preparation' => ['engaged', 'stopped'],
        'engaged' => ['closed', 'stopped'],
        'stopped' => ['engaged', 'preparation'],
        'closed' => ['engaged', 'preparation'],
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
