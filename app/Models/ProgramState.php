<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramState extends Model
{
    use Author, AutoFillable, GeneratesUuid, HasStaticTableName;

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_uuid', 'uuid');
    }
}
