<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Action extends Model
{
    use AutoFillable, GeneratesUuid, HasFactory, HasStaticTableName, Author;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('action')
            ->logOnly([
                'reference',
                'name',
                'priority',
                'risk_level',
                'description',
                'prerequisites',
                'impacts',
                'risks',
                'generate_document_type',
                'status',
                'start_date',
                'end_date',
                'budget',
                'currency_uuid',
                'structure_uuid',
                'action_plan_uuid'
            ])
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Action created: #{$this->id}",
                'updated' => "Action updated: #{$this->id}",
                'deleted' => "Action deleted: #{$this->id}",
                default => $eventName,
            })
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Les attributs castés automatiquement.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'frequency_value' => 'integer',
            'actual_progress_percent' => 'float',
            'realization_rate' => 'float',
            'realization_index' => 'float',
            'budget' => 'float',
            'is_planned' => 'boolean',
        ];
    }

    /**
     * Relations belongsTo
     */
    public function structure(): BelongsTo
    {
        return $this->belongsTo(Structure::class, 'structure_uuid', 'uuid');
    }

    public function responsibleStructure(): BelongsTo
    {
        return $this->belongsTo(Structure::class, 'responsible_structure_uuid', 'uuid');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_uuid', 'uuid');
    }

    public function actionPlan(): BelongsTo
    {
        return $this->belongsTo(ActionPlan::class, 'action_plan_uuid', 'uuid');
    }

    public function projectOwner(): BelongsTo
    {
        return $this->belongsTo(ProjectOwner::class, 'project_owner_uuid', 'uuid');
    }

    public function delegatedProjectOwner(): BelongsTo
    {
        return $this->belongsTo(DelegatedProjectOwner::class, 'delegated_project_owner_uuid', 'uuid');
    }

    public function actionDomain(): BelongsTo
    {
        return $this->belongsTo(ActionDomain::class, 'action_domain_uuid', 'uuid');
    }

    public function strategicDomain(): BelongsTo
    {
        return $this->belongsTo(StrategicDomain::class, 'strategic_domain_uuid', 'uuid');
    }

    public function capabilityDomain(): BelongsTo
    {
        return $this->belongsTo(CapabilityDomain::class, 'capability_domain_uuid', 'uuid');
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
        return $this->belongsToMany(Beneficiary::class, 'action_beneficiaries', 'action_uuid', 'beneficiary_uuid', 'uuid', 'uuid');
    }

    public function stakeholders(): BelongsToMany
    {
        return $this->belongsToMany(Stakeholder::class, 'action_stakeholders', 'action_uuid', 'stakeholder_uuid', 'uuid', 'uuid');
    }

    public function fundingSources(): BelongsToMany
    {
        return $this->belongsToMany(FundingSource::class, 'action_funding_sources', 'action_uuid', 'funding_source_uuid', 'uuid', 'uuid')
            ->withPivot('planned_budget');
    }

    public function statusChangedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'status_changed_by', 'uuid');
    }

    public function periods(): HasMany
    {
        return $this->hasMany(ActionPeriod::class, 'action_uuid', 'uuid');
    }

    public function phases(): HasMany
    {
        return $this->hasMany(ActionPhase::class, 'action_uuid', 'uuid');
    }

    public function decisions()
    {
        return $this->morphMany(Decision::class, 'decidable');
    }

    public function objectives(): BelongsToMany
    {
        return $this->belongsToMany(StrategicObjective::class, 'action_objective_alignments', 'action_uuid', 'objective_uuid', 'uuid', 'uuid');
    }

    public function expenseTypes()
    {
        return $this->belongsToMany(ExpenseType::class, 'action_expense_types', 'action_uuid', 'expense_type_uuid')
            ->withPivot('total')
            ->withTimestamps();
    }

    public function metric(): HasOne
    {
        return $this->hasOne(ActionMetric::class, 'action_uuid', 'uuid');
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(ActionStatus::class, 'action_uuid', 'uuid');
    }
}
