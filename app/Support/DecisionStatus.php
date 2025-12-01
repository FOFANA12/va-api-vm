<?php

namespace App\Support;

class DecisionStatus
{
    /**
     * List of available statuses with their localized names and display colors.
     */
    private static array $statuses = [
        [
            'code' => 'announced',
            'color' => '#03A9F4',
            'name' => [
                'fr' => 'Annoncé',
                'en' => 'Announced',
                'ar' => 'تم الإعلان',
            ],
        ],
        [
            'code' => 'not_processed',
            'color' => '#ff9800',
            'name' => [
                'fr' => 'Non traité',
                'en' => 'Not processed',
                'ar' => 'لم يتم المعالجة',
            ],
        ],
        [
            'code' => 'in_progress',
            'color' => '#2196f3',
            'name' => [
                'fr' => 'En cours de traitement',
                'en' => 'In progress',
                'ar' => 'قيد المعالجة',
            ],
        ],
        [
            'code' => 'processed',
            'color' => '#4caf50',
            'name' => [
                'fr' => 'Traité',
                'en' => 'Processed',
                'ar' => 'معالج',
            ],
        ],
        [
            'code' => 'cancelled',
            'color' => '#f44336',
            'name' => [
                'fr' => 'Annulé',
                'en' => 'Cancelled',
                'ar' => 'ملغى',
            ],
        ],
    ];

    /**
     * Allowed transitions between statuses.
     */
    private static array $transitions = [
        'announced' => ['not_processed', 'in_progress', 'cancelled'],
        'not_processed' => ['in_progress', 'cancelled'],
        'in_progress' => ['processed', 'cancelled'],
        'processed' => ['in_progress'],
        'cancelled' => ['in_progress'],
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
