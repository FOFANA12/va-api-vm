<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CapabilityDomainStatus extends Model
{
    use Author, AutoFillable, GeneratesUuid, HasStaticTableName;

    public function capabilityDomain(): BelongsTo
    {
        return $this->belongsTo(CapabilityDomain::class, 'capability_domain_uuid', 'uuid');
    }
}
