<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    /** @use HasFactory<\Database\Factories\SupplierFactory> */
    use HasFactory, Author, AutoFillable, GeneratesUuid, HasStaticTableName;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'capital' => 'decimal:2',
            'note' => 'decimal:2',
            'annual_turnover' => 'decimal:2',
            'status' => 'boolean',
        ];
    }

    public function contractType(): BelongsTo
    {
        return $this->belongsTo(ContractType::class, 'contract_type_uuid', 'uuid');
    }


    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'supplier_uuid', 'uuid');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(SupplierEvaluation::class, 'supplier_uuid', 'uuid');
    }
}
