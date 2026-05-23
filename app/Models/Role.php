<?php

namespace App\Models;

use App\Enums\RoleName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function isAdmin(): bool
    {
        return $this->slug === RoleName::Admin->value;
    }

    public function isManager(): bool
    {
        return $this->slug === RoleName::Manager->value;
    }

    public function isAccountant(): bool
    {
        return $this->slug === RoleName::Accountant->value;
    }
}
