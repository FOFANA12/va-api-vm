<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Structure extends Model
{
    use Author, AutoFillable, GeneratesUuid, HasStaticTableName;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('structure')
            ->logOnly(['abbreviation', 'name', 'parent_uuid', 'status'])
            ->setDescriptionForEvent(function (string $eventName) {
                return match ($eventName) {
                    'created' => "Structure created: #{$this->id}",
                    'updated' => "Structure updated: #{$this->id}",
                    'deleted' => "Structure deleted: #{$this->id}",
                    default => $eventName,
                };
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_uuid', 'uuid');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_uuid', 'uuid');
    }

    public function strategicMaps(): HasMany
    {
        return $this->hasMany(StrategicMap::class, 'structure_uuid', 'uuid');
    }

    public function actionPlans(): HasMany
    {
        return $this->hasMany(ActionPlan::class, 'structure_uuid', 'uuid');
    }
}
