<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;

class ActionStakeholder extends Model
{
    use  AutoFillable, HasStaticTableName, Author;



    public $timestamps = false;

    public function action()
    {
        return $this->belongsTo(Action::class, 'action_uuid', 'uuid');
    }
    public function stakeholder()
    {
        return $this->belongsTo(Stakeholder::class, 'stakeholder_uuid', 'uuid');
    }
}
