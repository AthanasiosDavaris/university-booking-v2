<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attirbutes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title'])]
class Building extends Model
{
    /**
     * Relationship: A Building has many Rooms
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
