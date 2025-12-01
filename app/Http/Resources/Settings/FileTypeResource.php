<?php

namespace App\Http\Resources\Settings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class FileTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'original_name' => $this->original_name,
            'download_url'  => $this->uuid
                ? URL::route('file-types.download', ['file_type' => $this->id])
                : null,
            'status' => $this->status,
        ];
    }
}
