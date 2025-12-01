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

class StrategicElement extends Model
{
    use Author, AutoFillable, GeneratesUuid, HasStaticTableName;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        $entityType = strtolower($this->type ?? 'axis');
        $logName = $entityType === 'lever' ? 'strategic_lever' : 'strategic_axis';

        return LogOptions::defaults()
            ->useLogName($logName)
            ->logOnly([
                'name',
                'abbreviation',
                'order',
                'description',
                'status',
                'structure_uuid',
                'strategic_map_uuid',
                'type',
                'parent_structure_uuid',
                'parent_map_uuid',
                'parent_element_uuid',
            ])
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => ucfirst($entityType) . " created: #{$this->id}",
                'updated' => ucfirst($entityType) . " updated: #{$this->id}",
                'deleted' => ucfirst($entityType) . " deleted: #{$this->id}",
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
            'order' => 'integer',
            'status' => 'boolean',
        ];
    }

    public function structure(): BelongsTo
    {
        return $this->belongsTo(Structure::class, 'structure_uuid', 'uuid');
    }

    public function strategicMap(): BelongsTo
    {
        return $this->belongsTo(StrategicMap::class, 'strategic_map_uuid', 'uuid');
    }

    public function objectives(): HasMany
    {
        return $this->hasMany(StrategicObjective::class, 'strategic_element_uuid', 'uuid');
    }

    public function parentStructure(): BelongsTo
    {
        return $this->belongsTo(Structure::class, 'parent_structure_uuid', 'uuid');
    }

    public function parentMap(): BelongsTo
    {
        return $this->belongsTo(StrategicMap::class, 'parent_map_uuid', 'uuid');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_element_uuid', 'uuid');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_element_uuid', 'uuid');
    }
}
