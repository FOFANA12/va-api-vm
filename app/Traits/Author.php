<?php

namespace App\Traits;

use App\Models\User;

trait Author
{
    /**
     * Utilisateur qui a créé l'enregistrement.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'uuid');
    }

    /**
     * Utilisateur qui a mis à jour l'enregistrement.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'uuid');
    }

    /**
     * Attribut virtuel : auteur (données minimales du créateur).
     *
     * @return array|null
     */
    public function getAuthorAttribute()
    {
        $user = $this->relationLoaded('createdBy') ? $this->createdBy : $this->createdBy()?->first();

        return $user
            ? [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
            ]
            : null;
    }
}
