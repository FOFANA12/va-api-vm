<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Indicator extends Model
{
    use AutoFillable, GeneratesUuid, HasFactory, HasStaticTableName, Author;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('indicator')
            ->logOnly([
                'reference',
                'name',
                'description',
                'chart_type',
                'frequency_unit',
                'frequency_value',
                'initial_value',
                'final_target_value',
                'achieved_value',
                'unit',
                'status',
                'state',
                'is_planned',
                'structure_uuid',
                'strategic_map_uuid',
                'strategic_element_uuid',
                'strategic_objective_uuid'
            ])
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Indicator created: #{$this->id}",
                'updated' => "Indicator updated: #{$this->id}",
                'deleted' => "Indicator deleted: #{$this->id}",
                default => $eventName,
            })
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Les attributs castés automatiquement.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'frequency_value' => 'integer',
            'initial_value' => 'float',
            'final_target_value' => 'float',
            'achieved_value' => 'float',
            'is_planned' => 'boolean',
        ];
    }

    /**
     * Relations belongsTo
     */
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

    public function strategicObjective(): BelongsTo
    {
        return $this->belongsTo(StrategicObjective::class, 'strategic_objective_uuid', 'uuid');
    }

    public function leadStructure(): BelongsTo
    {
        return $this->belongsTo(Structure::class, 'lead_structure_uuid', 'uuid');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(IndicatorCategory::class, 'category_uuid', 'uuid');
    }

    public function periods(): HasMany
    {
        return $this->hasMany(IndicatorPeriod::class, 'indicator_uuid', 'uuid');
    }

    public function statusChangedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'status_changed_by', 'uuid');
    }
}
