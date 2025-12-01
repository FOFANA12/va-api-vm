<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierEvaluation extends Model
{
    /** @use HasFactory<\Database\Factories\SupplierEvaluationFactory> */
    use HasFactory,  AutoFillable, Author, GeneratesUuid, HasStaticTableName;


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'score_delay' => 'integer',
            'score_price' => 'integer',
            'score_quality' => 'integer',
            'total_score' => 'decimal:2',
        ];
    }
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_uuid', 'uuid');
    }
    public function evaluatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by', 'uuid');
    }
}
