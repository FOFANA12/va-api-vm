<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionDomainStatus extends Model
{
    use Author, AutoFillable, GeneratesUuid, HasStaticTableName;

    public function actionDomain(): BelongsTo
    {
        return $this->belongsTo(ActionDomain::class, 'action_domain_uuid', 'uuid');
    }
}
