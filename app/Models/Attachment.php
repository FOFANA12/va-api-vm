<?php

namespace App\Models;

use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends Model
{
    use AutoFillable, GeneratesUuid, HasStaticTableName;
    public $timestamps = false;

    public function attachable(): MorphTo
    {
        return $this->morphTo(null, 'attachable_type', 'attachable_id', 'id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'uuid');
    }

     public function fileType(): BelongsTo
    {
        return $this->belongsTo(FileType::class, 'file_type_uuid', 'uuid');
    }
}
