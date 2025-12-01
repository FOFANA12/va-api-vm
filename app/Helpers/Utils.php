<?php

namespace App\Helpers;

use App\Models\Structure;
use Illuminate\Support\Collection;

class Utils
{
    /**
     * Retrieves the UUIDs of a structure and all its parent structures.
     *
     * @param  \App\Models\Structure  $structure
     * @return \Illuminate\Support\Collection
     */
    public static function getStructureAndParents(Structure $structure): Collection
    {
        $uuids = collect([$structure->uuid]);

        $current = $structure;
        while ($current && $current->parent_uuid) {
            $parent = Structure::where('uuid', $current->parent_uuid)->first();
            if (!$parent) {
                break;
            }

            $uuids->push($parent->uuid);
            $current = $parent;
        }

        return $uuids;
    }
}
