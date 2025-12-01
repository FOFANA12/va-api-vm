<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActionPhase extends Model
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
            'number' => 'integer',
            'weight' => 'float',
        ];
    }

    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class, 'action_uuid', 'uuid');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'phase_uuid', 'uuid');
    }
}
