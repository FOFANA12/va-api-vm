<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
     use AutoFillable, GeneratesUuid, HasStaticTableName, Author;


   
}
