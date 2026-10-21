<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessorAvailability extends Model
{
    protected $fillable = ['professor_id', 'day_of_week', 'start_time', 'end_time', 'is_preferred'];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'is_preferred' => 'boolean',
        ];
    }

    public function professor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professor_id');
    }
}