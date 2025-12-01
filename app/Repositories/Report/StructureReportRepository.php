<?php

namespace App\Repositories\Report;

use App\Models\Structure;

class StructureReportRepository
{
    /**
     * Load requirements data
     */
    public function requirements()
    {
        $structures = Structure::where('status', true)
            ->orderBy('id', 'desc')
            ->select('id', 'uuid', 'name', 'abbreviation')
            ->get();

        return [
            'structures' => $structures,
        ];
    }
}
