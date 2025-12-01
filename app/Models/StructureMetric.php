<?php

namespace App\Models;

use App\Traits\AutoFillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StructureMetric extends Model
{
   use AutoFillable;

    protected function casts(): array
    {
        return [
            'aligned_axes_count' => 'integer',
            'aligned_objectives_count' => 'integer',
            'aligned_maps_count' => 'integer',
        ];
    }

     public function structure(): BelongsTo
    {
        return $this->belongsTo(structure::class, 'structure_uuid', 'uuid');
    }
}
