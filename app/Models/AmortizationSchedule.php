<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmortizationSchedule extends Model
{
    protected $fillable = [
        'property_id',
        'principal',
        'interest_rate',
        'term_months',
        'start_date',
        'payment_frequency',
        'monthly_payment',
        'remaining_balance',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'principal' => 'decimal:2',
            'interest_rate' => 'decimal:4',
            'monthly_payment' => 'decimal:2',
            'remaining_balance' => 'decimal:2',
            'start_date' => 'date',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(AmortizationPayment::class)->orderBy('payment_number');
    }
}
