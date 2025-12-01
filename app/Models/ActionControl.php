<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActionControl extends Model
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
            'forecast_percent' => 'float',
            'actual_progress_percent' => 'float',
        ];
    }

    public function actionPeriod(): BelongsTo
    {
        return $this->belongsTo(ActionPeriod::class, 'action_period_uuid', 'uuid');
    }

    public function controlPhases(): HasMany
    {
        return $this->hasMany(ActionControlPhase::class, 'action_control_uuid', 'uuid');
    }

    public function attachment()
    {
        return $this->morphOne(Attachment::class, 'attachable');
    }
}
