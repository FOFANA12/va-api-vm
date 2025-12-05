<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElementaryLevelState extends Model
{
    use Author, AutoFillable, GeneratesUuid, HasStaticTableName;

    public function elementaryLevel(): BelongsTo
    {
        return $this->belongsTo(ElementaryLevel::class, 'elementary_level_uuid', 'uuid');
    }
}
