<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\TrustTransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class TrustTransaction extends Model
{
    protected $fillable = [
        'trust_number',
        'transaction_type',
        'client_id',
        'property_id',
        'transaction_date',
        'amount',
        'payment_method',
        'reference',
        'notes',
        'journal_entry_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'transaction_type' => TrustTransactionType::class,
            'transaction_date' => 'date',
            'amount' => 'decimal:2',
            'payment_method' => PaymentMethod::class,
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function journalEntryReference(): MorphOne
    {
        return $this->morphOne(JournalEntry::class, 'referenceable');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
