<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelognsTo;

#[Fillable(['building_id', 'name', 'capacity', 'projector'])]
class Room extends Model
{
    /**
     * Relationship: A Room belongs to a Building
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }
}
