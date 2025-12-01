<?php

namespace App\Http\Requests;

use App\Models\Action;
use App\Models\Currency;
use App\Models\FundingSource;
use Illuminate\Foundation\Http\FormRequest;

class ActionFundReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'receipt_date' => 'required|date',
            'validity_date' => 'required|date|after_or_equal:receipt_date',
            'funding_source' => 'bail|required|exists:' . FundingSource::tableName() . ',uuid',
            'currency' => 'bail|required|exists:' . Currency::tableName() . ',uuid',
            'exchange_rate' => 'required|numeric|gt:0',
            'amount_original' => 'required|numeric|min:0',
        ];

        if ($this->isMethod('post')) {
            $rules += [
                'action' => 'bail|required|exists:' . Action::tableName() . ',uuid',
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'action' => __('app/action_fund_receipt.request.action'),
            'receipt_date' => __('app/action_fund_receipt.request.receipt_date'),
            'validity_date' => __('app/action_fund_receipt.request.validity_date'),
            'funding_source' => __('app/action_fund_receipt.request.funding_source'),
            'currency' => __('app/action_fund_receipt.request.currency'),
            'exchange_rate' => __('app/action_fund_receipt.request.exchange_rate'),
            'amount_original' => __('app/action_fund_receipt.request.amount_original'),
        ];
    }
}
