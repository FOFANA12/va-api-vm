<?php

namespace App\Http\Resources\Auth;

use App\Helpers\FileHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'lang' => $this->lang,
            'avatar_url' => $this->avatar ? FileHelper::avatarUrl('avatars/' . $this->avatar) : null,
            'is_employee' => $this->relationLoaded('employee') && !is_null($this->employee),
            'employee' => $this->when(
                $this->relationLoaded('employee') && $this->employee,
                function () {
                    return [
                        'job_title' => $this->employee->job_title,
                        'floor' => $this->employee->floor,
                        'office' => $this->employee->office,
                        'structure' => [
                            'id' => $this->employee->structure?->id,
                            'uuid' => $this->employee->structure?->uuid,
                            'name' => $this->employee->structure?->name,
                            'abbreviation' => $this->employee->structure?->abbreviation
                        ],
                    ];
                }
            ),
            'role' => $this->whenLoaded('role', function () {
                return [
                    'name' => $this->role->name,
                    'permissions' => $this->role->permissions->pluck('name')->toArray(),
                ];
            }),
        ];
    }
}
