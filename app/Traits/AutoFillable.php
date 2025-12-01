<?php

namespace App\Traits;

use Illuminate\Support\Facades\Schema;

trait AutoFillable
{
    /**
     * Automatically sets the fillable property for the model
     * based on the table's columns.
     *
     * @return array
     */
    public function getFillable()
    {
        return Schema::getColumnListing($this->getTable());
    }
}
