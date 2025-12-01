<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Decision extends Model
{

    use AutoFillable, GeneratesUuid, HasStaticTableName, Author;

    public function decidable(): MorphTo
    {
        return $this->morphTo('decidable', 'decidable_type', 'decidable_id', 'id');
    }

    public function statusChangedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'status_changed_by', 'uuid');
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(DecisionStatus::class, 'decision_uuid', 'uuid');
    }

    public function attachment()
    {
        return $this->morphOne(Attachment::class, 'attachable');
    }
}
