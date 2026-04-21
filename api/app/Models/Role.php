<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title'])]
class Role extends Model
{
    /**
     * Relationship: A Role has many Users
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relationship: A Role belongs to many Permissions
     */
    public function perimissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }
}
