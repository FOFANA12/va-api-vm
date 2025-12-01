<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Currency extends Model
{
    use Author, AutoFillable, GeneratesUuid, HasStaticTableName;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('currency')
            ->logOnly(['name', 'code', 'is_default', 'status'])
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Currency created: #{$this->id}",
                'updated' => "Currency updated: #{$this->id}",
                'deleted' => "Currency deleted: #{$this->id}",
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
            'is_default' => 'boolean',
        ];
    }
}
