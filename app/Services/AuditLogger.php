<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    /**
     * Log an activity in a centralized way
     *
     */
    public static function log(
        string $message,
        $user = null,
        ?Model $subject = null,
        ?string $event = null,
        array $extra = []
    ): void {
        $properties = array_merge([
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ], $extra);

        $activity = activity()
            ->causedBy($user ?? auth()->user())
            ->withProperties($properties);

        if ($subject) {
            $activity->performedOn($subject);
        }

        if ($event) {
            $activity->event($event);
        }

        $activity->log($message);
    }
}
