<?php

namespace App\Models;

use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Employee extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
    use AutoFillable, GeneratesUuid, HasFactory, HasStaticTableName;
    use LogsActivity;

    public $timestamps = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['job_title', 'structure_uuid', 'floor', 'office', 'can_logged_in'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Employee created: #{$this->id}",
                'updated' => "Employee updated: #{$this->id}",
                'deleted' => "Employee deleted: #{$this->id}",
                default => $eventName,
            })
            ->useLogName('employee')
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'can_logged_in' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function structure(): BelongsTo
    {
        return $this->belongsTo(Structure::class, 'structure_uuid', 'uuid');
    }
}
