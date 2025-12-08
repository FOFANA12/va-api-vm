<?php

namespace App\Services;

use App\Models\Structure;
use App\Models\User;

class StructureAccessService
{
    public function getAccessibleStructureUuids(User $user): ?array
    {
        if (!$user->employee) {
            return null;
        }

        $root = $user->employee->structure_uuid;
        $children = $this->getDescendants($root);

        return array_merge([$root], $children);
    }

    private function getDescendants(string $uuid): array
    {
        $children = Structure::where('parent_uuid', $uuid)->pluck('uuid')->toArray();

        if (!$children) {
            return [];
        }

        $nested = [];
        foreach ($children as $child) {
            $nested = array_merge($nested, $this->getDescendants($child));
        }

        return array_merge($children, $nested);
    }
}
