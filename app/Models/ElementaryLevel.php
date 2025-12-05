<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ElementaryLevel extends Model
{
    use GeneratesUuid, AutoFillable, HasStaticTableName, Author;

    protected $casts = [
        'budget' => 'float',
    ];

    public function capabilityDomain()
    {
        return $this->belongsTo(CapabilityDomain::class, 'capability_domain_uuid', 'uuid');
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_uuid', 'uuid');
    }
    
    public function beneficiaries(): BelongsToMany
    {
        return $this->belongsToMany(Beneficiary::class, 'elementary_level_beneficiaries', 'elementary_level_uuid', 'beneficiary_uuid', 'uuid', 'uuid');
    }

    public function fundingSources(): BelongsToMany
    {
        return $this->belongsToMany(FundingSource::class, 'elementary_level_funding_sources', 'elementary_level_uuid', 'funding_source_uuid', 'uuid', 'uuid')
            ->withPivot('planned_budget');
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(ElementaryLevelStatus::class, 'elementary_level_uuid', 'uuid');
    }

    public function states(): HasMany
    {
        return $this->hasMany(ElementaryLevelState::class, 'elementary_level_uuid', 'uuid');
    }

    public function statusChangedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'status_changed_by', 'uuid');
    }

    public function stateChangedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'state_changed_by', 'uuid');
    }
}
