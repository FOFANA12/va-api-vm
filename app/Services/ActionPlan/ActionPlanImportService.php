<?php

namespace App\Services\ActionPlan;

use App\Helpers\FileHelper;
use App\Jobs\ImportActionPlanJob;
use App\Models\ActionPlan;
use App\Models\ImportLog;
use Illuminate\Support\Facades\Auth;

class ActionPlanImportService
{
    protected string $basePath = 'uploads/imports';

    /**
     * Handle file import request
     */
    public function handleImport($file, string $structure)
    {
        $identifier = FileHelper::uploadLocal($file, $this->basePath);

        $log = ImportLog::create([
            'import_type' => ActionPlan::tableName(),
            'status' => 'pending',
            'message' => __('app/import.pending'),
            'identifier' => $identifier,
            'created_by' => Auth::user()?->uuid,
        ]);

        ImportActionPlanJob::dispatch($log->uuid, $structure);

        return $log;
    }
}
