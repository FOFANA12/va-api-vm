<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class ContractResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $mode = $this->additional['mode'] ?? $request->input('mode', 'view');

        return match ($mode) {
            'list' => $this->forList(),
            'edit' => $this->forEdit(),
            default => $this->forView(),
        };
    }

    private function forList(): array
    {
        $startDate = $this->start_date ? DateTimeFormatter::formatDate($this->start_date) : null;
        $endDate = $this->end_date ? DateTimeFormatter::formatDate($this->end_date) : null;
        $signedAt = $this->signed_at ? DateTimeFormatter::formatDate($this->signed_at) : null;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'contract_number' => $this->contract_number,
            'title' => $this->title,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'amount' => $this->amount,
            'signed_at' => $signedAt,
            'status' => $this->status,
            'download_url'  => $this->attachment_id
                ? URL::route('attachments.download', ['attachment' => $this->attachment_id])
                : null,
        ];
    }

    private function forEdit(): array
    {

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'contract_number' => $this->contract_number,
            'title' => $this->title,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'amount' => $this->amount,
            'signed_at' => $this->signed_at,
            'description' => $this->description,
            'status' => $this->status,
            'attachment' => $this->whenLoaded('attachment', fn() => new AttachmentResource($this->attachment)),

        ];
    }

    private function forView(): array
    {
        $startDate = $this->start_date ? DateTimeFormatter::formatDate($this->start_date) : null;
        $endDate = $this->end_date ? DateTimeFormatter::formatDate($this->end_date) : null;
        $signedAt = $this->signed_at ? DateTimeFormatter::formatDate($this->signed_at) : null;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'contract_number' => $this->contract_number,
            'title' => $this->title,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'amount' => $this->amount,
            'signed_at' => $signedAt,
            'description' => $this->description,
            'status' => $this->status,
            'attachment' => $this->whenLoaded('attachment', fn() => new AttachmentResource($this->attachment)),

        ];
    }
}
