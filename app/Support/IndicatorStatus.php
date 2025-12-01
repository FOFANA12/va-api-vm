<?php

namespace App\Support;

class IndicatorStatus
{
    /**
     * List of available statuses with their localized names and display colors.
     */
    private static array $statuses = [
        [
            'code' => 'created',
            'color' => '#42a5f5',
            'name' => [
                'fr' => 'Créé',
                'en' => 'Created',
                'ar' => 'تم الإنشاء',
            ],
        ],
        [
            'code' => 'planned',
            'color' => '#7e57c2',
            'name' => [
                'fr' => 'Planifié',
                'en' => 'Planned',
                'ar' => 'مخطط',
            ],
        ],
        [
            'code' => 'in_progress',
            'color' => '#2196f3',
            'name' => [
                'fr' => 'En réalisation',
                'en' => 'In progress',
                'ar' => 'قيد التنفيذ',
            ],
        ],
        [
            'code' => 'closed',
            'color' => '#4caf50',
            'name' => [
                'fr' => 'Clôturé',
                'en' => 'Closed',
                'ar' => 'مختوم',
            ],
        ],
        [
            'code' => 'stopped',
            'color' => '#f44336',
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
        'created' => ['planned'],           // Créé → Planifié
        'planned' => ['in_progress'],       // Planifié → En réalisation
        'in_progress' => ['stopped', 'closed'], // En réalisation → Arrêt ou Clôturé
        'stopped' => ['in_progress', 'closed'], // Arrêt → Reprise ou Clôturé
        'closed' => [],
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
