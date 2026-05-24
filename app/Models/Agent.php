<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agent extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'license_number',
        'commission_rate',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'commission_rate' => 'decimal:4',
            'is_active' => 'boolean',
        ];
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
