<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DelegatedProjectOwner extends Model
{
    use Author, AutoFillable, GeneratesUuid, HasStaticTableName;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('delegated_project_owner')
            ->logOnly(['project_owner_uuid', 'name', 'email', 'phone', 'status'])
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Delegate project owner created: #{$this->id}",
                'updated' => "Delegate project owner updated: #{$this->id}",
                'deleted' => "Delegate project owner deleted: #{$this->id}",
                default => $eventName,
            })
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Casts.
     */
    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function projectOwner(): BelongsTo
    {
        return $this->belongsTo(ProjectOwner::class, 'project_owner_uuid', 'uuid');
    }
}
