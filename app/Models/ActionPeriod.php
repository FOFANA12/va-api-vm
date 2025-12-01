<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ActionPeriod extends Model
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
            'progress_percent' => 'float',
            'actual_progress_percent' => 'float',
        ];
    }

    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class, 'action_uuid', 'uuid');
    }

    public function controls(): HasOne
    {
        return $this->hasOne(ActionControl::class, 'action_period_uuid', 'uuid');
    }

    public function isLast(): bool
    {
        $lastPeriod = $this->action
            ->periods()
            ->orderByDesc('end_date')
            ->orderByDesc('id')
            ->first();

        return $lastPeriod && $lastPeriod->id === $this->id;
    }
}
