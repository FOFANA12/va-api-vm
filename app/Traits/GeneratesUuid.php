<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GeneratesUuid
{
    /**
     * Boot function to automatically generate UUID when creating a model.
     */
    protected static function boot()
    {
        if (method_exists(parent::class, 'boot')) {
            parent::boot();
        }

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
