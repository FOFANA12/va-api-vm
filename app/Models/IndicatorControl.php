<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndicatorControl extends Model
{
    use AutoFillable, GeneratesUuid, HasStaticTableName, Author;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'target_value' => 'float',
            'achieved_value' => 'float',
        ];
    }

    public function indicatorPeriod(): BelongsTo
    {
        return $this->belongsTo(IndicatorPeriod::class, 'indicator_period_uuid', 'uuid');
    }

    public function attachment()
    {
        return $this->morphOne(Attachment::class, 'attachable');
    }
}
