<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Room extends Model
{
    protected $fillable = [
        'building_id', 'department_id', 'name', 'capacity', 'exam_capacity', 
        'floor', 'type', 'has_projector', 'has_lab_equipment', 'has_ac', 
        'is_wheelchair_accessible', 'has_wheelchair_desks', 'computer_count', 
        'hvac_type', 'svg_element_id'
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'exam_capacity' => 'integer',
            'floor' => 'integer',
            'computer_count' => 'integer',
            'has_projector' => 'boolean',
            'has_lab_equipment' => 'boolean',
            'has_ac' => 'boolean',
            'is_wheelchair_accessible' => 'boolean',
            'has_wheelchair_desks' => 'boolean',
        ];
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    // Checks if room is free by ensuring it doesn't have any overlapping confirmed reservations
    public function scopeAvailableDuring(Builder $query, string $startsAt, string $endsAt): Builder
    {
        return $query->whereDoesntHave('reservations', function (Builder $q) use ($startsAt, $endsAt) {
            $q->confirmed()->overlapping($startsAt, $endsAt);
        });
    }
}