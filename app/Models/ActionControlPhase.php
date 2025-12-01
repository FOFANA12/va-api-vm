<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionControlPhase extends Model
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
            'weight' => 'float',
        ];
    }

    public function actionControl(): BelongsTo
    {
        return $this->belongsTo(ActionControl::class, 'action_control_uuid', 'uuid');
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(ActionPhase::class, 'phase_uuid', 'uuid');
    }
}
