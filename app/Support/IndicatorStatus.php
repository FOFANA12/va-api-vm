<?php

namespace App\Support;

class IndicatorStatus
{
    public const CREATED = 'created';
    public const PLANNED = 'planned';
    public const IN_PROGRESS = 'in_progress';
    public const CLOSED = 'closed';
    public const STOPPED = 'stopped';

    /**
     * List of available statuses with their localized names and display colors.
     */
    private static array $statuses = [
        [
            'code' => self::CREATED,
            'color' => '#42a5f5',
            'name' => [
                'fr' => 'Créé',
                'en' => 'Created',
                'ar' => 'تم الإنشاء',
            ],
        ],
        [
            'code' => self::PLANNED,
            'color' => '#7e57c2',
            'name' => [
                'fr' => 'Planifié',
                'en' => 'Planned',
                'ar' => 'مخطط',
            ],
        ],
        [
            'code' => self::IN_PROGRESS,
            'color' => '#2196f3',
            'name' => [
                'fr' => 'En réalisation',
                'en' => 'In progress',
                'ar' => 'قيد التنفيذ',
            ],
        ],
        [
            'code' => self::CLOSED,
            'color' => '#4caf50',
            'name' => [
                'fr' => 'Clôturé',
                'en' => 'Closed',
                'ar' => 'مختوم',
            ],
        ],
        [
            'code' => self::STOPPED,
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
        self::CREATED => [self::PLANNED],           // Créé → Planifié
        self::PLANNED => [self::IN_PROGRESS],       // Planifié → En réalisation
        self::IN_PROGRESS => [self::STOPPED, self::CLOSED], // En réalisation → Arrêt ou Clôturé
        self::STOPPED => [self::IN_PROGRESS, self::CLOSED], // Arrêt → Reprise ou Clôturé
        self::CLOSED => [],
    ];

    private static array $objectiveStatusMatrix = [
        StrategicObjectiveStatus::DECLARED => [
            self::CREATED,
            self::PLANNED,
        ],
        StrategicObjectiveStatus::ENGAGED => [
            self::CREATED,
            self::PLANNED,
            self::IN_PROGRESS,
            self::CLOSED,
            self::STOPPED,
        ],
        StrategicObjectiveStatus::CLOSED => [],
        StrategicObjectiveStatus::STOPPED => [],
    ];

    /**
     * Get all statuses as an array.
     */
    public static function all(): array
    {
        return self::$statuses;
    }

    /**
     * Get the initial status code for a new indicator.
     */
    public static function initial(): string
    {
        return self::CREATED;
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
     * Get statuses that can be selected manually.
     */
    public static function manualNext(string $code): array
    {
        return array_values(array_filter(
            self::next($code),
            fn(string $nextCode) => $nextCode !== self::PLANNED
        ));
    }

    /**
     * Check if transition is allowed.
     */
    public static function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::$transitions[$from] ?? [], true);
    }

    public static function isAllowedForObjectiveStatus(string $indicatorStatus, ?string $objectiveStatus): bool
    {
        return in_array($indicatorStatus, self::$objectiveStatusMatrix[$objectiveStatus] ?? [], true);
    }

}
