<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role_id',
        'department_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->first_name} {$this->last_name}",
        );
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function courseSessions(): HasMany
    {
        return $this->hasMany(CourseSession::class, 'professor_id');
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(ProfessorAvailability::class, 'professor_id');
    }

    // RBAC Helpers
    public function isAdmin(): bool { return $this->role?->name === 'admin'; }
    public function isSecretary(): bool { return $this->role?->name === 'secretary'; }
    public function isProfessor(): bool { return $this->role?->name === 'professor'; }
    public function isStudent(): bool { return $this->role?->name === 'student'; }
}