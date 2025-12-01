<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class ActionFundDisbursementResource extends JsonResource
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
            'uuid' => $this->uuid,
            'reference' => $this->reference,
            'operation_number' => $this->operation_number,
            'contract_number' => $this->contract_number,
            'supplier' => $this->supplier,
            'payment_amount' =>  $this->payment_amount,
            'execution_date' => DateTimeFormatter::formatDate($this->execution_date),
            'payment_date' => DateTimeFormatter::formatDate($this->payment_date),
            'currency' => $this->currency,
            'phase' => $this->phase,
            'task' => $this->task,
            'payment_mode' => $this->payment_mode,

            'action' => [
                'reference' => $this->action_reference,
                'name' => $this->action_name,
                'id' => $this->action_id,
            ],
            'export_word_url'  => $this->id
                ? URL::route('actionFundDisbursement.exportToWord', ['actionFundDisbursement' => $this->id])
                : null,

            'download_url'  => $this->attachment_id
                ? URL::route('attachments.download', ['attachment' => $this->attachment_id])
                : null,
        ];
    }

    protected function forEdit(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'reference' => $this->reference,
            'operation_number' => $this->operation_number,
            'signature_date' => $this->signature_date,
            'execution_date' => $this->execution_date,
            'payment_date' => $this->payment_date,
            'payment_amount' => $this->payment_amount,
            'cheque_reference' => $this->cheque_reference,
            'description' => $this->description,
            'action' => $this->whenLoaded('action', fn() => [
                'uuid' => $this->action->uuid,
                'reference' => $this->action->reference,
                'name' => $this->action->name,
                'phases' => $this->action->phases,
                'currency' => $this->action->currency,
                'contract_type_uuid' => $this->action->contract_type_uuid,
            ]),
            'payment_mode' => $this->payment_mode_uuid,
            'budget_type' => $this->budget_type_uuid,
            'phase' => $this->phase_uuid,
            'task' => $this->task_uuid,
            'supplier' => $this->supplier_uuid,
            'contract' => $this->contract_uuid,
            'expense_types' => $this->expenseTypes->map(function ($item) {
                return [
                    'uuid' => $item->uuid,
                    'name' => $item->name,
                ];
            }),
            'attachment' => $this->whenLoaded('attachment', fn() => new AttachmentResource($this->attachment)),
        ];
    }

    protected function forView(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'reference' => $this->reference,
            'operation_number' => $this->operation_number,
            'signature_date' => DateTimeFormatter::formatDate($this->signature_date),
            'execution_date' => DateTimeFormatter::formatDate($this->execution_date),
            'payment_date' => DateTimeFormatter::formatDate($this->payment_date),
            'payment_amount' => (float) $this->payment_amount,
            'cheque_reference' => $this->cheque_reference,
            'description' => $this->description,
            'action' => $this->whenLoaded('action', fn() => [
                'uuid' => $this->action->uuid,
                'reference' => $this->action->reference,
                'name' => $this->action->name,
                'currency' => $this->action->currency,
            ]),
            'payment_mode' => $this->whenLoaded('paymentMode', fn() => $this->paymentMode->name),
            'budget_type' => $this->whenLoaded('budgetType', fn() => $this->budgetType->name),
            'phase' => $this->whenLoaded('phase', fn() => $this->phase->name),
            'task' => $this->whenLoaded('task', fn() => $this->task->title),

            'supplier' => $this->whenLoaded('supplier', fn() => $this->supplier->company_name),
            'contract' => $this->whenLoaded('contract', fn() => $this->contract->contract_number),

            'expense_types' => $this->expenseTypes->map(fn($item) => ['name' => $item->name]),
            'attachment' => $this->whenLoaded('attachment', fn() => new AttachmentResource($this->attachment)),
        ];
    }
}
