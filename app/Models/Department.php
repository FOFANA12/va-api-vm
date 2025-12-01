<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Department extends Model
{
    use Author, AutoFillable, GeneratesUuid, HasStaticTableName, HasFactory;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('department')
            ->logOnly(['region_uuid', 'name', 'latitude', 'longitude', 'status'])
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Department created: #{$this->id}",
                'updated' => "Department updated: #{$this->id}",
                'deleted' => "Department deleted: #{$this->id}",
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
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_uuid', 'uuid');
    }

    public function municipalities(): HasMany
    {
        return $this->hasMany(Municipality::class, 'department_uuid', 'uuid');
    }
}
