<?php

namespace App\Support;

class ActionStatus
{
    public const DRAFT = 'draft';
    public const CREATED = 'created';
    public const PLANNED = 'planned';
    public const IN_PROGRESS = 'in_progress';
    public const STOPPED = 'stopped';
    public const CLOSED = 'closed';

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
            'code' => 'created',
            'color' => '#42a5f5',
            'name' => [
                'fr' => 'Créée',
                'en' => 'Created',
                'ar' => 'تم الإنشاء',
            ],
        ],
        [
            'code' => 'planned',
            'color' => '#7e57c2',
            'name' => [
                'fr' => 'Planifiée',
                'en' => 'Planned',
                'ar' => 'مخططة',
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
            'code' => 'stopped',
            'color' => '#f44336',
            'name' => [
                'fr' => 'En arrêt',
                'en' => 'Stopped',
                'ar' => 'متوقفة',
            ],
        ],
        [
            'code' => 'closed',
            'color' => '#4caf50',
            'name' => [
                'fr' => 'Clôturée',
                'en' => 'Closed',
                'ar' => 'مختومة',
            ],
        ],
    ];

    /**
     * Allowed transitions between statuses.
     */
    private static array $transitions = [
        'draft' => ['created'],      // Depuis Brouillon → uniquement Créée
        'created' => ['planned'],    // Après création → Planifiée
        'planned' => ['in_progress'], // Après planification → En réalisation
        'in_progress' => ['stopped', 'closed'], // Peut arrêter ou clôturer
        'stopped' => ['in_progress', 'closed'], // Peut reprendre ou clôturer
        'closed' => [],              // Final
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
     * Get statuses that can be selected manually (excludes planned, set automatically via planning).
     */
    public static function manualNext(string $code): array
    {
        return array_values(array_filter(
            self::next($code),
            fn(string $nextCode) => $nextCode !== self::PLANNED
        ));
    }

    /**
     * Get statuses that prevent financial movements on an action.
     */
    public static function fundMovementBlockedStatuses(): array
    {
        return [self::DRAFT, self::CREATED, self::PLANNED, self::STOPPED, self::CLOSED];
    }

    /**
     * Check whether receipts and disbursements are locked for a status.
     */
    public static function blocksFundMovements(?string $status): bool
    {
        return in_array($status, self::fundMovementBlockedStatuses(), true);
    }

    /**
     * Check if transition is allowed.
     */
    public static function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::$transitions[$from] ?? [], true);
    }
}
