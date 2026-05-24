<?php

namespace App\Models;

use App\Enums\RoleName;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
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
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole(RoleName|string $role): bool
    {
        $slug = $role instanceof RoleName ? $role->value : $role;

        return $this->role?->slug === $slug;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(RoleName::Admin);
    }

    public function isManager(): bool
    {
        return $this->hasRole(RoleName::Manager);
    }

    public function isAccountant(): bool
    {
        return $this->hasRole(RoleName::Accountant);
    }

    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    public function canApproveTransactions(): bool
    {
        return $this->isAdmin() || $this->isManager();
    }

    public function canReverseTransactions(): bool
    {
        return $this->isAdmin() || $this->isManager();
    }

    public function canViewReports(): bool
    {
        return $this->isAdmin() || $this->isManager() || $this->isAccountant();
    }
}
