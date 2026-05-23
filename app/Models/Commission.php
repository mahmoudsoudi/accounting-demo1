<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    protected $fillable = [
        'property_id',
        'agent_id',
        'listing_id',
        'sale_price',
        'commission_rate',
        'brokerage_amount',
        'agent_amount',
        'status',
        'journal_entry_id',
    ];

    protected function casts(): array
    {
        return [
            'sale_price' => 'decimal:2',
            'commission_rate' => 'decimal:4',
            'brokerage_amount' => 'decimal:2',
            'agent_amount' => 'decimal:2',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }
}
