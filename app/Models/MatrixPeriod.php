<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MatrixPeriod extends Model
{

    use AutoFillable, GeneratesUuid, HasStaticTableName, Author;


    public function strategicMap(): BelongsTo
    {
        return $this->belongsTo(StrategicMap::class, 'strategic_map_uuid', 'uuid');
    }

    public function strategicObjectives(): HasMany
    {
        return $this->hasMany(StrategicObjective::class, 'matrix_period_uuid', 'uuid');
    }
}
