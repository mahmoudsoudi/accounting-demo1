<?php

namespace App\Models;

use App\Enums\AccountType;
use App\Enums\NormalBalance;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasTranslations;

    protected $fillable = [
        'code',
        'name',
        'name_translations',
        'account_type',
        'parent_id',
        'normal_balance',
        'is_active',
        'description',
        'description_translations',
    ];

    protected function casts(): array
    {
        return [
            'account_type' => AccountType::class,
            'normal_balance' => NormalBalance::class,
            'is_active' => 'boolean',
            'name_translations' => 'array',
            'description_translations' => 'array',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalLine::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getNameAttribute(?string $value): string
    {
        return $this->getTranslation('name') ?: ($value ?? '');
    }

    public function getDescriptionAttribute(?string $value): ?string
    {
        $description = $this->getTranslation('description');

        if ($description !== '') {
            return $description;
        }

        return $value;
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->code} - {$this->name}";
    }
}
