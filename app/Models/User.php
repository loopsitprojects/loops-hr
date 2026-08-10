<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    const ROLE_SUPER_ADMIN = 'Super Admin';
    const ROLE_HR_ADMIN = 'HR Admin';
    const ROLE_MANAGER = 'Operations Manager';
    const ROLE_MANAGERS = 'Managers';
    const ROLE_HOD = 'Head of Department';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_super_admin',
        'role',
        'department_id',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin || $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isAdmin(): bool
    {
        return $this->is_super_admin || 
               $this->role === self::ROLE_SUPER_ADMIN || 
               $this->role === self::ROLE_HR_ADMIN;
    }

    public function isHR(): bool
    {
        return $this->role === self::ROLE_HR_ADMIN;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isManagers(): bool
    {
        return $this->role === self::ROLE_MANAGERS;
    }

    public function isHOD(): bool
    {
        return $this->role === self::ROLE_HOD;
    }

    /**
     * Route notifications for the mail channel.
     *
     * @return string|array|null
     */
    public function routeNotificationForMail()
    {
        if ($this->email === 'admin@loopshr.com') {
            return null;
        }

        return $this->email;
    }

    public function canViewAllDepartments(): bool
    {
        return $this->isAdmin() || $this->isHR() || $this->isManager() || $this->isManagers();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
