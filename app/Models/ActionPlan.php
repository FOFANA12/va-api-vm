<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ActionPlan extends Model
{
    use Author, AutoFillable, GeneratesUuid, HasStaticTableName;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('action_plan')
            ->logOnly(['structure_uuid', 'responsible_uuid', 'reference', 'name', 'description', 'start_date', 'end_date', 'status'])
           ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Action plan created: #{$this->id}",
                'updated' => "Action plan updated: #{$this->id}",
                'deleted' => "Action plan deleted: #{$this->id}",
                default => $eventName,
            })
            ->logOnlyDirty()
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
            'status' => 'boolean',
        ];
    }

    public function structure(): BelongsTo
    {
        return $this->belongsTo(Structure::class, 'structure_uuid', 'uuid');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_uuid', 'uuid');
    }

    public function decisions(): MorphMany
    {
        return $this->morphMany(Decision::class, 'decidable');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(Action::class, 'action_plan_uuid', 'uuid');
    }
}
