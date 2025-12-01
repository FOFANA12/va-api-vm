<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;

class IndicatorStatus extends Model
{

    use Author, AutoFillable, GeneratesUuid, HasStaticTableName;

    protected $casts = [
        'status_code' => 'string',
    ];

    public function indicator()
    {
        return $this->belongsTo(Indicator::class, 'indicator_uuid', 'uuid');
    }
}
