<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class StrategicObjective extends Model
{
    use  Author, AutoFillable, GeneratesUuid, HasStaticTableName;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('strategic_objective')
            ->logOnly([
                'reference',
                'name',
                'description',
                'priority',
                'risk_level',
                'status',
                'start_date',
                'end_date',
                'structure_uuid',
                'strategic_map_uuid',
                'strategic_axis_uuid',
                'lead_structure_uuid'
            ])
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Strategic Objective created: #{$this->id}",
                'updated' => "Strategic Objective updated: #{$this->id}",
                'deleted' => "Strategic Objective deleted: #{$this->id}",
                default => $eventName,
            })
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function structure(): BelongsTo
    {
        return $this->belongsTo(Structure::class, 'structure_uuid', 'uuid');
    }

    public function strategicMap(): BelongsTo
    {
        return $this->belongsTo(StrategicMap::class, 'strategic_map_uuid', 'uuid');
    }

    public function strategicElement(): BelongsTo
    {
        return $this->belongsTo(StrategicElement::class, 'strategic_element_uuid', 'uuid');
    }

    public function leadStructure(): BelongsTo
    {
        return $this->belongsTo(Structure::class, 'lead_structure_uuid', 'uuid');
    }

    public function statusChangedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'status_changed_by', 'uuid');
    }

    public function decisions(): MorphMany
    {
        return $this->morphMany(Decision::class, 'decidable');
    }

    public function matrixPeriod(): BelongsTo
    {
        return $this->belongsTo(MatrixPeriod::class, 'matrix_period_uuid', 'uuid');
    }

    public function indicators(): HasMany
    {
        return $this->hasMany(Indicator::class, 'strategic_objective_uuid', 'uuid');
    }

    public function actions(): BelongsToMany
    {
        return $this->belongsToMany(Action::class, 'action_objective_alignments', 'objective_uuid', 'action_uuid', 'uuid', 'uuid');
    }
}
