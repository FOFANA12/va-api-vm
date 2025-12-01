<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ActionFundDisbursement extends Model
{
    use AutoFillable, GeneratesUuid, HasFactory, HasStaticTableName, Author;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('action_fund_disbursement')
            ->logOnly([
                'reference',
                'contract_number',
                'operation_number',
                'supplier_name',
                'tax_number',
                'signature_date',
                'execution_date',
                'payment_date',
                'payment_amount',
                'payment_mode_uuid',
                'cheque_reference',
                'budget_type_uuid',
                'phase_uuid',
                'description',
                'action_uuid',
            ])
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Action fund disbursement created: #{$this->id}",
                'updated' => "Action fund disbursement updated: #{$this->id}",
                'deleted' => "Action fund disbursement deleted: #{$this->id}",
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
            'payment_amount' => 'float',
        ];
    }

    /**
     * Relations belongsTo
     */
    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class, 'action_uuid', 'uuid');
    }

    public function attachment()
    {
        return $this->morphOne(Attachment::class, 'attachable');
    }

    public function paymentMode(): BelongsTo
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode_uuid', 'uuid');
    }

    public function budgetType(): BelongsTo
    {
        return $this->belongsTo(BudgetType::class, 'budget_type_uuid', 'uuid');
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(ActionPhase::class, 'phase_uuid', 'uuid');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_uuid', 'uuid');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_uuid', 'uuid');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_uuid', 'uuid');
    }

    public function expenseTypes(): BelongsToMany
    {
        return $this->belongsToMany(ExpenseType::class, 'action_fund_disbursement_expense_types', 'action_fund_disbursement_uuid', 'expense_type_uuid', 'uuid', 'uuid');
    }
}
