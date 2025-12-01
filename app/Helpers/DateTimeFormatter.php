<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateTimeFormatter
{
    /**
     * Format a date according to the application's locale.
     *
     * @param  string  $date  The date to format.
     * @param  string  $format  The format to apply (default: 'D/MM/YYYY').
     * @return string|null Returns the formatted date, or null if the input is invalid.
     */
    public static function formatDate($date, string $format = 'DD/MM/YYYY'): ?string
    {
        try {
            $locale = app()->getLocale();

            return Carbon::parse($date)->locale($locale)->isoFormat($format);
        } catch (\Exception $e) {
            return null; // Returns null if the date is invalid.
        }
    }

    /**
     * Format a datetime according to the application's locale.
     *
     * @param  string  $datetime  The datetime to format.
     * @param  string  $format  The format to apply (default: 'D/MM/YYYY [at] HH:mm').
     * @return string|null Returns the formatted datetime, or null if the input is invalid.
     */
    public static function formatDatetime($datetime, string $format = 'DD/MM/YYYY [à] HH:mm'): ?string
    {
        try {
            $locale = app()->getLocale();

            return Carbon::parse($datetime)->locale($locale)->isoFormat($format);
        } catch (\Exception $e) {
            return null; // Returns null if the datetime is invalid.
        }
    }
}
