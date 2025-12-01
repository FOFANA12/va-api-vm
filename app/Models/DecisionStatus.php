<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DecisionStatus extends Model
{
    use Author, AutoFillable, GeneratesUuid, HasStaticTableName;

    public function decision(): BelongsTo
    {
        return $this->belongsTo(Decision::class, 'decision_uuid', 'uuid');
    }

    public function attachment()
    {
        return $this->morphOne(Attachment::class, 'attachable');
    }
}
