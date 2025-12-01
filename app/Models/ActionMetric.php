<?php

namespace App\Models;

use App\Traits\AutoFillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionMetric extends Model
{
    use AutoFillable;
    /**
     * Les attributs castés automatiquement.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'realization_rate' => 'float',
            'realization_index' => 'float',
            'aligned_axes_count' => 'integer',
            'aligned_objectives_count' => 'integer',
            'aligned_maps_count' => 'integer',
        ];
    }

    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class, 'action_uuid', 'uuid');
    }
}
