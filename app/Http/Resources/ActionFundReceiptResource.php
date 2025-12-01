<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class ActionFundReceiptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $mode = $this->additional['mode'] ?? $request->input('mode', 'view');

        return match ($mode) {
            'list' => $this->forList(),
            'edit' => $this->forEdit(),
            default => $this->forView(),
        };
    }

    protected function forList(): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'funding_source' => $this->funding_source,
            'amount_original' => $this->amount_original,
            'converted_amount' => $this->converted_amount,
            'currency' => $this->currency,
            'receipt_date' => DateTimeFormatter::formatDate($this->receipt_date),
            'validity_date' => DateTimeFormatter::formatDate($this->validity_date),
            'action' => [
                'reference' => $this->action_reference,
                'name' => $this->action_name,
                'id' => $this->action_id,
            ],
            'export_word_url'  => $this->id
                ? URL::route('actionFundReceipt.exportToWord', ['actionFundReceipt' => $this->id])
                : null,
        ];
    }

    protected function forEdit(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'reference' => $this->reference,
            'action' => $this->whenLoaded('action', fn() => [
                'uuid' => $this->action->uuid,
                'reference' => $this->action->reference,
                'name' => $this->action->name,
                'currency' => $this->action->currency,
            ]),
            'receipt_date' => $this->receipt_date,
            'validity_date' => $this->validity_date,
            'funding_source' => $this->funding_source_uuid,
            'currency' => $this->currency_uuid,
            'exchange_rate' => floatval($this->exchange_rate),
            'amount_original' => $this->amount_original,
            'converted_amount' => $this->converted_amount,
        ];
    }

    protected function forView(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'reference' => $this->reference,
            'action' => $this->whenLoaded('action', fn() => [
                'uuid' => $this->action->uuid,
                'reference' => $this->action->reference,
                'name' => $this->action->name,
                'currency' => $this->action->currency,
            ]),
            'receipt_date' => DateTimeFormatter::formatDate($this->receipt_date),
            'validity_date' => DateTimeFormatter::formatDate($this->validity_date),
            'funding_source' => $this->whenLoaded('fundingSource', fn() => $this->fundingSource->name),
            'currency' => $this->whenLoaded('currency', fn() => [
                'uuid' => $this->currency->uuid,
                'code' => $this->currency->code,
                'name' => $this->currency->name,
            ]),
            'exchange_rate' => floatval($this->exchange_rate),
            'amount_original' => $this->amount_original,
            'converted_amount' => $this->converted_amount,
        ];
    }
}
