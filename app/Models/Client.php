<?php

namespace App\Models;

use App\Enums\TrustTransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function trustTransactions(): HasMany
    {
        return $this->hasMany(TrustTransaction::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function trustBalanceFromTransactions(): float
    {
        return (float) $this->trustTransactions->sum(fn (TrustTransaction $transaction) => match ($transaction->transaction_type) {
            TrustTransactionType::Deposit => (float) $transaction->amount,
            TrustTransactionType::Disbursement, TrustTransactionType::Transfer => -(float) $transaction->amount,
        });
    }

    public static function trustBalanceSelectExpression(): string
    {
        return "COALESCE(SUM(CASE
            WHEN transaction_type = 'deposit' THEN amount
            WHEN transaction_type IN ('disbursement', 'transfer') THEN -amount
            ELSE 0
        END), 0)";
    }
}
