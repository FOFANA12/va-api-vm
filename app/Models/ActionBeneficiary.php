<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;

class ActionBeneficiary extends Model
{
    use  AutoFillable, HasStaticTableName, Author;



    public $timestamps = false;

    public function action()
    {
        return $this->belongsTo(Action::class, 'action_uuid', 'uuid');
    }
    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_uuid', 'uuid');
    }
}
