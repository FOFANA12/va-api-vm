<?php

namespace App\Models;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{
    /** @use HasFactory<\Database\Factories\ContractFactory> */
    use HasFactory, Author, AutoFillable, GeneratesUuid, HasStaticTableName;


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => 'boolean',
        ];
    }
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_uuid', 'uuid');
    }

    public function attachment()
    {
        return $this->morphOne(Attachment::class, 'attachable');
    }
}
