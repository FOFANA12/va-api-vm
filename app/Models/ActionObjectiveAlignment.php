<?php

namespace App\Models;

use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionObjectiveAlignment extends Model
{
    use AutoFillable, GeneratesUuid, HasStaticTableName;
    public $timestamps = false;

    public function actionStructure(): BelongsTo
    {
        return $this->belongsTo(Structure::class, 'action_structure_uuid', 'uuid');
    }

     public function objectiveStructure(): BelongsTo
    {
        return $this->belongsTo(Structure::class, 'objective_structure_uuid', 'uuid');
    }

    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class, 'action_uuid', 'uuid');
    }


    public function strategicMap(): BelongsTo
    {
        return $this->belongsTo(StrategicMap::class, 'strategic_map_uuid', 'uuid');
    }
   
    public function strategicElement(): BelongsTo
    {
        return $this->belongsTo(StrategicElement::class, 'strategic_element_uuid', 'uuid');
    }

     public function strategicObjective(): BelongsTo
    {
        return $this->belongsTo(StrategicObjective::class, 'objective_uuid', 'uuid');
    }
    
    public function alignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aligned_by', 'uuid');
    }
}
