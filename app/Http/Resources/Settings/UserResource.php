<?php

namespace App\Http\Resources\Settings;

use App\Support\Language;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentLang = app()->getLocale();

        return match ($request->mode) {
            'list' => $this->forList($currentLang),
            'edit' => $this->forEdit(),
            default => $this->forView($currentLang),
        };
    }

    private function forList($currentLang): array
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'lang' => Language::name($this->lang, $currentLang),
            'status' => $this->status,
        ];
    }

    private function forEdit(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'role' => $this->role_uuid,
            'email' => $this->email,
            'phone' => $this->phone,
            'lang' => $this->lang,
            'avatar' => $this->avatar,
            'status' => $this->status,
        ];
    }

    private function forView($currentLang): array
    {
        $role = $this->whenLoaded('role');

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'role' => $role?->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'lang' => Language::name($this->lang, $currentLang),
            'avatar' => $this->avatar,
            'status' => $this->status,
        ];
    }
}
