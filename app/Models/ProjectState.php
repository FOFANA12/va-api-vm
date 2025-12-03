<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectState extends Model
{
    use Author, AutoFillable, GeneratesUuid, HasStaticTableName;

    public function strategicDomain(): BelongsTo
    {
        return $this->belongsTo(StrategicDomain::class, 'strategic_domain_uuid', 'uuid');
    }
}
