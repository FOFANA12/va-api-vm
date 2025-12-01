<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Beneficiary extends Model
{
    use Author, AutoFillable, GeneratesUuid, HasStaticTableName, HasFactory;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('beneficiary')
            ->logOnly(['name', 'status'])
             ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Beneficiary created: #{$this->id}",
                'updated' => "Beneficiary updated: #{$this->id}",
                'deleted' => "Beneficiary deleted: #{$this->id}",
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
}
