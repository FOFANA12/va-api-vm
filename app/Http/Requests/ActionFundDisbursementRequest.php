<?php

namespace App\Http\Requests;

use App\Models\Action;
use App\Models\ActionPhase;
use App\Models\BudgetType;
use App\Models\Contract;
use App\Models\PaymentMode;
use App\Models\Supplier;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class ActionFundDisbursementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        if ($this->expense_types) {
            $this->merge([
                'expense_types' => json_decode($this->expense_types, true),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'operation_number' => 'bail|required|string|max:50',
            'signature_date' => 'bail|required|date',
            'execution_date' => 'bail|required|date|after:signature_date',
            'payment_date' => 'bail|required|date|after:execution_date',
            'payment_amount' => 'bail|required|numeric|gt:0',
            'cheque_reference' => 'bail|required|string|max:50',
            'description' => 'bail|nullable|string|max:500',
            'payment_mode' => 'bail|required|exists:' . PaymentMode::tableName() . ',uuid',
            'budget_type' => 'bail|required|exists:' . BudgetType::tableName() . ',uuid',
            'phase' => 'bail|required|exists:' . ActionPhase::tableName() . ',uuid',
            'task' => 'bail|nullable|exists:' . Task::tableName() . ',uuid',
            'supplier' => 'bail|required|exists:' . Supplier::tableName() . ',uuid',
            'contract' => 'bail|required|exists:' . Contract::tableName() . ',uuid',
            'file' => 'bail|nullable|file|max:5120|mimes:jpeg,png,jpg,pdf,xls,xlsx,doc,docx',
        ];

        if ($this->isMethod('post')) {
            $rules += [
                'action' => 'bail|required|exists:' . Action::tableName() . ',uuid',
            ];
        }
    }


    public function attributes(): array
    {
        return [
            'action' => __('app/action_fund_disbursement.request.action'),
            'operation_number' => __('app/action_fund_disbursement.request.operation_number'),
            'signature_date' => __('app/action_fund_disbursement.request.signature_date'),
            'execution_date' => __('app/action_fund_disbursement.request.execution_date'),
            'payment_date' => __('app/action_fund_disbursement.request.payment_date'),
            'payment_amount' => __('app/action_fund_disbursement.request.payment_amount'),
            'payment_mode' => __('app/action_fund_disbursement.request.payment_mode'),
            'cheque_reference' => __('app/action_fund_disbursement.request.cheque_reference'),
            'budget_type' => __('app/action_fund_disbursement.request.budget_type'),
            'phase' => __('app/action_fund_disbursement.request.phase'),
            'task' => __('app/action_fund_disbursement.request.task'),
            'description' => __('app/action_fund_disbursement.request.description'),

            'contract' => __('app/action_fund_disbursement.request.contract'),
            'supplier' => __('app/action_fund_disbursement.request.supplier'),
            'file' => __('app/attachment.request.file'),
        ];
    }
}
