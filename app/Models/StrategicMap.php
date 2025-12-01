<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class StrategicMap extends Model
{
    use Author, AutoFillable, GeneratesUuid, HasStaticTableName;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('strategic_map')
            ->logOnly(['name', 'description', 'start_date', 'end_date', 'status', 'structure_uuid'])
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Strategic Map created: #{$this->id}",
                'updated' => "Strategic Map updated: #{$this->id}",
                'deleted' => "Strategic Map deleted: #{$this->id}",
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

    public function elements(): HasMany
    {
        return $this->hasMany(StrategicElement::class, 'strategic_map_uuid', 'uuid');
    }

     public function objectives(): HasMany
    {
        return $this->hasMany(StrategicObjective::class, 'strategic_map_uuid', 'uuid');
    }

    public function stakeholders(): HasMany
    {
        return $this->hasMany(StrategicStakeholder::class, 'strategic_map_uuid', 'uuid');
    }

    public function matrixPeriods(): HasMany
    {
        return $this->hasMany(MatrixPeriod::class, 'strategic_map_uuid', 'uuid');
    }
}
