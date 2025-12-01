<?php

namespace App\Models;

use App\Traits\AutoFillable;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    use HasStaticTableName, AutoFillable;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'password_reset_tokens';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'email';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Automatically set the `created_at` field on model creation.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = now();
        });

        static::updating(function ($model) {
            $model->created_at = now();
        });
    }

    /**
     * Scope to check if a token is still valid (not expired).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $email
     * @param string $token
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeValidToken($query, $email, $token)
    {
        return $query->where('email', $email)
            ->where('token', $token)
            ->where('created_at', '>', now()->subMinutes(config('auth.passwords.users.expire', 60)));
    }
}
