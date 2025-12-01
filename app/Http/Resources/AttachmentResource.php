<?php

namespace App\Http\Resources;

use App\Helpers\DateTimeFormatter;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class AttachmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $uploadedBy = $request->mode === 'list'
            ? $this->uploader_name
            : optional($this->whenLoaded('uploadedBy'))->name;

        $fileType = $request->mode === 'list'
            ? $this->file_type
            : optional($this->whenLoaded('fileType'))->name;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'attachable_type' => $this->attachable_type,
            'attachable_id' => $this->attachable_id,
            'title' => $this->title,
            'original_name' => $this->original_name,
            'mime_type' => $this->mime_type,
            'file_type' => $fileType,
            'comment' => $this->comment,
            'size' => $this->size,
            'uploaded_at' => DateTimeFormatter::formatDatetime($this->uploaded_at),
            'uploaded_by' => $uploadedBy,
            'download_url'  => $this->uuid
                ? URL::route('attachments.download', ['attachment' => $this->id])
                : null,
        ];
    }
}
