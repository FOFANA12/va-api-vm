<?php

namespace App\Services;

use App\Models\Structure;
use App\Models\User;

class StructureAccessService
{
    public function getAccessibleStructureUuids(
        User $user,
        bool $includeParents = false,
        bool $includeChildren = true
    ): ?array {
        if (!$user->employee || !$user->employee->structure_uuid) {
            return null;
        }

        $rootUuid = $user->employee->structure_uuid;

        $result = [$rootUuid];


        if ($includeParents) {
            $result = array_merge(
                $result,
                $this->getAncestors($rootUuid)
            );
        }

        if ($includeChildren) {
            $result = array_merge(
                $result,
                $this->getDescendants($rootUuid)
            );
        }

        return array_values(array_unique($result));
    }

    private function getDescendants(string $uuid): array
    {
        $children = Structure::where('parent_uuid', $uuid)->pluck('uuid');

        return $children
            ->flatMap(fn($childUuid) => [
                $childUuid,
                ...$this->getDescendants($childUuid),
            ])
            ->toArray();
    }

    private function getAncestors(string $uuid): array
    {
        $parentUuid = Structure::where('uuid', $uuid)->value('parent_uuid');

        if (!$parentUuid) {
            return [];
        }

        return [
            $parentUuid,
            ...$this->getAncestors($parentUuid),
        ];
    }
}
