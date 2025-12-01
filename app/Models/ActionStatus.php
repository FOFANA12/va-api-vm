<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;

class ActionStatus extends Model
{

    use Author, AutoFillable, GeneratesUuid, HasStaticTableName;

    protected $casts = [
        'status_code' => 'string',
    ];

    public function action()
    {
        return $this->belongsTo(Action::class, 'action_uuid', 'uuid');
    }
}
