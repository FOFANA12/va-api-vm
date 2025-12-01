<?php

namespace App\Traits;

trait HasStaticTableName
{
    /**
     * Get the table name of the model statically.
     *
     * @return string
     *
     * @throws \Exception
     */
    public static function tableName()
    {
        if (! is_subclass_of(static::class, \Illuminate\Database\Eloquent\Model::class)) {
            throw new \Exception(static::class." must extend Illuminate\Database\Eloquent\Model to use this trait.");
        }

        return (new static)->getTable();
    }
}
