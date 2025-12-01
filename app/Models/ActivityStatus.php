<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityStatus extends Model
{
    use Author, AutoFillable, GeneratesUuid, HasStaticTableName;

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'activity_uuid', 'uuid');
    }
}
