<?php

namespace App\Support;

class RiskLevel
{
    /**
     * Supported risks with their localized names.
     */
    private static array $risks = [
        [
            'code' => 'very_low',
            'level' => 1,
            'color' => '#9e9e9e', // gris
            'name' => [
                'fr' => 'Très faible',
                'en' => 'Very Low',
                'ar' => 'منخفض جدًا',
            ],
        ],
        [
            'code' => 'low',
            'level' => 2,
            'color' => '#2196f3', // bleu clair
            'name' => [
                'fr' => 'Faible',
                'en' => 'Low',
                'ar' => 'منخفض',
            ],
        ],
        [
            'code' => 'moderate',
            'level' => 3,
            'color' => '#ffc107', // orange
            'name' => [
                'fr' => 'Modéré',
                'en' => 'Moderate',
                'ar' => 'معتدل',
            ],
        ],
        [
            'code' => 'high',
            'level' => 4,
            'color' => '#ff9800', // orange foncé
            'name' => [
                'fr' => 'Élevé',
                'en' => 'High',
                'ar' => 'مرتفع',
            ],
        ],
        [
            'code' => 'very_high',
            'level' => 5,
            'color' => '#f44336', // rouge
            'name' => [
                'fr' => 'Très élevé',
                'en' => 'Very High',
                'ar' => 'مرتفع جدًا',
            ],
        ],
        [
            'code' => 'critical',
            'level' => 6,
            'color' => '#b71c1c', // rouge foncé
            'name' => [
                'fr' => 'Critique',
                'en' => 'Critical',
                'ar' => 'حرج',
            ],
        ],
    ];

    /**
     * Return all risk levels.
     */
    public static function all(): array
    {
        return self::$risks;
    }

    /**
     * Return only the risk level codes.
     */
    public static function codes(): array
    {
        return array_column(self::$risks, 'code');
    }

    /**
     * Return the localized label for a given risk level.
     */
    public static function name(string $code, string $locale = 'fr'): ?string
    {
        foreach (self::$risks as $risk) {
            if ($risk['code'] === $code) {
                return $risk['name'][$locale] ?? $risk['name']['fr'];
            }
        }

        return null;
    }

    /**
     * Return a single risk level as an object with code, label, level, and color.
     */
    public static function get(string $code, string $locale = 'fr'): ?object
    {
        foreach (self::$risks as $risk) {
            if ($risk['code'] === $code) {
                return (object) [
                    'code' => $risk['code'],
                    'label' => $risk['name'][$locale] ?? $risk['name']['fr'],
                    'level' => $risk['level'],
                    'color' => $risk['color'],
                ];
            }
        }

        return null;
    }
}
