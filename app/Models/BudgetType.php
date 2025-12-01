<?php

namespace App\Models;

use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BudgetType extends Model
{
    use AutoFillable, GeneratesUuid, HasStaticTableName, HasFactory;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('budget_type')
            ->logOnly(['name', 'status'])
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Budget type created: #{$this->id}",
                'updated' => "Budget type updated: #{$this->id}",
                'deleted' => "Budget type deleted: #{$this->id}",
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
