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

class ActionDomain extends Model
{

    use  Author, AutoFillable, GeneratesUuid, HasStaticTableName;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('program')
            ->logOnly([
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
                'created' => "Program created: #{$this->id}",
                'updated' => "Program updated: #{$this->id}",
                'deleted' => "Program deleted: #{$this->id}",
                default => $eventName,
            })
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'budget' => 'float',
        ];
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_uuid', 'uuid');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_uuid', 'uuid');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_uuid', 'uuid');
    }

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class, 'municipality_uuid', 'uuid');
    }

    public function beneficiaries(): BelongsToMany
    {
        return $this->belongsToMany(Beneficiary::class, 'program_beneficiaries', 'action_domain_uuid', 'beneficiary_uuid', 'uuid', 'uuid');
    }

    public function fundingSources(): BelongsToMany
    {
        return $this->belongsToMany(FundingSource::class, 'program_funding_sources', 'action_domain_uuid', 'funding_source_uuid', 'uuid', 'uuid')
            ->withPivot('planned_budget');
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(ProgramStatus::class, 'action_domain_uuid', 'uuid');
    }

    public function states(): HasMany
    {
        return $this->hasMany(ProgramState::class, 'action_domain_uuid', 'uuid');
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
