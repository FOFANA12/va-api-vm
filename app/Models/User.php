<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\Author;
use App\Traits\AutoFillable;
use App\Traits\GeneratesUuid;
use App\Traits\HasStaticTableName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Notifiable, HasApiTokens, AutoFillable, GeneratesUuid, HasFactory, HasStaticTableName, Author, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'phone', 'status', 'lang', 'avatar'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "User created: #{$this->id}",
                'updated' => "User updated: #{$this->id}",
                'deleted' => "User deleted: #{$this->id}",
                default => $eventName,
            })
            ->useLogName('user')
            ->dontSubmitEmptyLogs();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'user_uuid', 'uuid');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_uuid', 'uuid');
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    public function hasPermission(string $permission): bool
    {
        return $this->role
            && $this->role->permissions->contains('name', $permission);
    }
}
