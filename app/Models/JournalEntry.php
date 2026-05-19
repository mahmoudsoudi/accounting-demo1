<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = [
        'entry_number',
        'date',
        'description',
        'status',
        'posted_at',
        'reversed_at',
        'reversal_of_id',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'posted_at' => 'datetime',
        'reversed_at' => 'datetime',
    ];

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function reversalOf()
    {
        return $this->belongsTo(JournalEntry::class, 'reversal_of_id');
    }

    public function reversals()
    {
        return $this->hasMany(JournalEntry::class, 'reversal_of_id');
    }

    public function isPosted(): bool
    {
        return $this->status === 'posted';
    }
}
