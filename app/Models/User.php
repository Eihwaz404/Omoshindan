<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active', 'permissions'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
            'is_active' => 'boolean',
            'permissions' => 'array',
        ];
    }

    /**
     * Determine whether the user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if (in_array($this->role, ['super_admin', 'admin'], true)) {
            return true;
        }

        return in_array($permission, $this->permissions ?? [], true);
    }

    /**
     * Determine whether the user can manage access data.
     */
    public function canManageUsers(): bool
    {
        return $this->hasPermission('users.view');
    }

    /**
     * Determine whether the user belongs to the technical team.
     */
    public function isTechnical(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'support'], true);
    }

    /**
     * Determine whether the user can work a specific support area.
     */
    public function canWorkSupportArea(string $area): bool
    {
        if (! $this->isTechnical()) {
            return false;
        }

        return $this->hasPermission('support.areas.'.$area);
    }
}
