<?php

namespace App\Http\Requests;

use App\Models\ContractType;
use App\Models\Supplier;
use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $supplier = $this->route('supplier');

        $rules = [
            'company_name' => 'bail|required|string|max:255',
            'register_number' => 'bail|nullable|string|max:50',
            'establishment_year' => 'bail|nullable|digits:4|integer|min:1900|max:' . date('Y'),
            'capital' => 'bail|required|numeric|min:0',
            'annual_turnover' => 'bail|required|numeric|min:0',
            'employees_count' => 'bail|nullable|integer|min:0',
            'contract_type' => 'bail|required|uuid|exists:' . ContractType::tableName() . ',uuid',
            'name' => 'bail|required|string|max:100',
            'phone' => 'bail|required|string|max:20',
            'whatsapp' => 'bail|nullable|string|max:20',
            'email' => 'bail|nullable|email|max:100',
            'address' => 'bail|nullable|string|max:100',
        ];

        if ($this->isMethod('put')) {
            $rules += [
                'tax_number' => 'bail|required|string|max:8|unique:' . Supplier::tableName() . ',tax_number,' . $supplier->id


            ];
        } else {
            $rules += [
                'tax_number' => 'bail|required|string|max:8|unique:' . Supplier::tableName() . ',tax_number',

            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'company_name' => __('app/supplier.request.company_name'),
            'tax_number' => __('app/supplier.request.tax_number'),
            'register_number' => __('app/supplier.request.register_number'),
            'establishment_year' => __('app/supplier.request.establishment_year'),
            'capital' => __('app/supplier.request.capital'),
            'annual_turnover' => __('app/supplier.request.annual_turnover'),
            'employees_count' => __('app/supplier.request.employees_count'),
            'note' => __('app/supplier.request.note'),
            'contract_type' => __('app/supplier.request.contract_type'),
            'name' => __('app/supplier.request.name'),
            'phone' => __('app/supplier.request.phone'),
            'whatsapp' => __('app/supplier.request.whatsapp'),
            'email' => __('app/supplier.request.email'),
            'address' => __('app/supplier.request.address'),
        ];
    }
}
