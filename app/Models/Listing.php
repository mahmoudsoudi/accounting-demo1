<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Listing extends Model
{
    protected $fillable = [
        'property_id',
        'agent_id',
        'listing_type',
        'list_price',
        'status',
        'listed_at',
        'sold_at',
    ];

    protected function casts(): array
    {
        return [
            'list_price' => 'decimal:2',
            'listed_at' => 'date',
            'sold_at' => 'date',
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
}
