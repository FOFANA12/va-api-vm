<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class IndicatorPeriod extends Model
{
    use AutoFillable, GeneratesUuid, HasStaticTableName, Author;
    public $timestamps = false;

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

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class, 'indicator_uuid', 'uuid');
    }

    public function controls(): HasOne
    {
        return $this->hasOne(IndicatorControl::class, 'indicator_period_uuid', 'uuid');
    }

    public function isLast(): bool
    {
        $lastPeriod = $this->indicator
            ->periods()
            ->orderByDesc('end_date')
            ->orderByDesc('id')
            ->first();

        return $lastPeriod && $lastPeriod->id === $this->id;
    }
}
