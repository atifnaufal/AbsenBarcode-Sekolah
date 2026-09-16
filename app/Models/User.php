<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'avatar',
        'password',
        'firebase_uid',
        'role',
        'identifier',
        'class_name',
        'guardian_name',
        'active',
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
            'role' => UserRole::class,
            'active' => 'boolean',
        ];
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN_SEKOLAH;
    }

    public function canScan(): bool
    {
        return $this->role?->canScan() ?? false;
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) return null;

        // If it's a full URL (from Cloudinary), return as is
        if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
            return $this->avatar;
        }

        // Otherwise return local storage URL
        return asset('storage/' . $this->avatar);
    }
}
