<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StrategicStakeholder extends Model
{
    use Author, AutoFillable, GeneratesUuid, HasStaticTableName;

    public function strategicMap(): BelongsTo
    {
        return $this->belongsTo(StrategicMap::class, 'strategic_map_uuid', 'uuid');
    }
}
