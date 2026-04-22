<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelognsTo;

#[Fillable([
    'title',
    'room_id',
    'user_id',
    'day_of_week',
    'start_at',
    'end_at',
    'from',
    'to',
    'comment',
    'status'
])]
class Reservation extends Model
{
    /**
     * Relationship: A Reservation belongs to a Room
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Relationship: A Reservation belongs to a User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
