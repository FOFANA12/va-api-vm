<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ContractType extends Model
{
    use Author, AutoFillable, GeneratesUuid, HasStaticTableName, HasFactory;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('contract_type')
            ->logOnly(['name', 'status'])
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Contract type created: #{$this->id}",
                'updated' => "Contract type updated: #{$this->id}",
                'deleted' => "Contract type deleted: #{$this->id}",
                default => $eventName,
            })
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Casts.
     */
    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }


    public function procurementModes(): HasMany
    {
        return $this->hasMany(ProcurementMode::class, 'contract_type_uuid', 'uuid');
    }
}
