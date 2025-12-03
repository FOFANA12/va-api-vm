<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CapabilityDomain extends Model
{
    use GeneratesUuid, AutoFillable, HasStaticTableName, Author;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('activity')
            ->logOnly([
                'strategic_domain_uuid',
                'reference',
                'name',
                'description',
                'start_date',
                'end_date',
                'budget',
                'currency_uuid',
                'status',
                'responsible_uuid'
            ])
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Activity created: #{$this->id}",
                'updated' => "Activity updated: #{$this->id}",
                'deleted' => "Activity deleted: #{$this->id}",
                default => $eventName,
            })
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $casts = [
        'budget' => 'float',
    ];

    public function strategicDomain()
    {
        return $this->belongsTo(StrategicDomain::class, 'strategic_domain_uuid', 'uuid');
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_uuid', 'uuid');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_uuid', 'uuid');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_uuid', 'uuid');
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_uuid', 'uuid');
    }

    public function beneficiaries(): BelongsToMany
    {
        return $this->belongsToMany(Beneficiary::class, 'activity_beneficiaries', 'capability_domain_uuid', 'beneficiary_uuid', 'uuid', 'uuid');
    }

    public function fundingSources(): BelongsToMany
    {
        return $this->belongsToMany(FundingSource::class, 'activity_funding_sources', 'capability_domain_uuid', 'funding_source_uuid', 'uuid', 'uuid')
            ->withPivot('planned_budget');
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(ActivityStatus::class, 'capability_domain_uuid', 'uuid');
    }

    public function states(): HasMany
    {
        return $this->hasMany(ActivityState::class, 'capability_domain_uuid', 'uuid');
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
