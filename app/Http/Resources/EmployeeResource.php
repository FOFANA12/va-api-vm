<?php

namespace App\Http\Resources;

use App\Support\Language;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return match ($request->mode) {
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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'structure' => $this->structure,
            'job_title' => $this->job_title,
            'status' => (bool) $this->status,
        ];
    }

    protected function forEdit(): array
    {
        $user = $this->whenLoaded('user');

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'lang' => $user->lang,
            'avatar' => $user->avatar,
            'role' => $user->role_uuid,
            'structure' => $this->structure_uuid,
            'job_title' => $this->job_title,
            'floor' => $this->floor,
            'office' => $this->office,
            'can_logged_in' => $this->can_logged_in,
            'status' => (bool) $user->status,
        ];
    }

    protected function forView(): array
    {
        $user = $this->whenLoaded('user');
        $structure = $this->whenLoaded('structure');
        $role = $this->whenLoaded('');

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'lang' => Language::name($user->lang, app()->getLocale()),
            'avatar' => $user->avatar,
            'structure' => $structure->name,
            'role' => $user->role ? $user?->role?->name : '',
            'job_title' => $this->job_title,
            'floor' => $this->floor,
            'office' => $this->office,
            'can_logged_in' => $this->can_logged_in,
            'status' => (bool) $user->status,
        ];
    }
}
