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

class ActionFundReceipt extends Model
{
    use  AutoFillable, GeneratesUuid, HasStaticTableName, Author;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('action_fund_receipt')
            ->logOnly([
                'reference',
                'receipt_date',
                'validity_date',
                'funding_source_uuid',
                'currency_uuid',
                'exchange_rate',
                'amount_original',
                'converted_amount',
                'action_uuid',
            ])
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Action fund receipt created: #{$this->id}",
                'updated' => "Action fund receipt updated: #{$this->id}",
                'deleted' => "Action fund receipt deleted: #{$this->id}",
                default => $eventName,
            })
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class, 'action_uuid', 'uuid');
    }

    public function fundingSource(): BelongsTo
    {
        return $this->belongsTo(FundingSource::class, 'funding_source_uuid', 'uuid');
    }
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_uuid', 'uuid');
    }
}
