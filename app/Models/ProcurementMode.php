<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProcurementMode extends Model
{
    use Author, AutoFillable, GeneratesUuid, HasStaticTableName;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('procurement_mode')
            ->logOnly(['contract_type_uuid', 'name', 'duration', 'status'])
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Procurement mode created: #{$this->id}",
                'updated' => "Procurement mode updated: #{$this->id}",
                'deleted' => "Procurement mode deleted: #{$this->id}",
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
            'duration' => 'integer',
        ];
    }

    /**
     * Contract Type
     */
    public function contractType(): BelongsTo
    {
        return $this->belongsTo(ContractType::class, 'contract_type_uuid', 'uuid');
    }
}
