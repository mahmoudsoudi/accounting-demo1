<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class AmortizationPayment extends Model
{
    protected $fillable = [
        'amortization_schedule_id',
        'payment_number',
        'payment_date',
        'payment_amount',
        'principal_amount',
        'interest_amount',
        'remaining_balance',
        'journal_entry_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'payment_amount' => 'decimal:2',
            'principal_amount' => 'decimal:2',
            'interest_amount' => 'decimal:2',
            'remaining_balance' => 'decimal:2',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(AmortizationSchedule::class, 'amortization_schedule_id');
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function journalEntryReference(): MorphOne
    {
        return $this->morphOne(JournalEntry::class, 'referenceable');
    }
}
