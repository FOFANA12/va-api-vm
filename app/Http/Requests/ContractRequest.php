<?php

namespace App\Http\Requests;

use App\Models\Contract;
use Illuminate\Foundation\Http\FormRequest;

class ContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $contract = $this->route('contract');

        $rules = [
            'title' => 'bail|required|string|max:255',
            'start_date' => 'bail|nullable|date',
            'end_date' => 'bail|nullable|date|after_or_equal:start_date',
            'amount' => 'bail|nullable|numeric|min:0',
            'description' => 'bail|nullable|string|max:1000',
            'signed_at' => 'bail|nullable|date',
            'file' => 'bail|nullable|file|max:5120|mimes:jpeg,png,jpg,pdf,xls,xlsx,doc,docx',
        ];
        if ($this->isMethod('put')) {
            $rules += [
                'contract_number' => 'bail|required|string|max:20|unique:' . Contract::tableName() . ',contract_number,' . $contract->id


            ];
        } else {
            $rules += [
                'contract_number' => 'bail|required|string|max:20|unique:' . Contract::tableName() . ',contract_number',

            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'contract_number' => __('app/contract.request.contract_number'),
            'title' => __('app/contract.request.title'),
            'start_date' => __('app/contract.request.start_date'),
            'end_date' => __('app/contract.request.end_date'),
            'amount' => __('app/contract.request.amount'),
            'description' => __('app/contract.request.description'),
            'signed_at' => __('app/contract.request.signed_at'),
                'file' => __('app/contract.request.file'),
        ];
    }
}
