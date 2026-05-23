<?php

namespace App\Models;

use App\Enums\JournalEntryStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class JournalEntry extends Model
{
    protected $fillable = [
        'reference_number',
        'transaction_date',
        'status',
        'notes',
        'attachment_path',
        'created_by',
        'approved_by',
        'approved_at',
        'reversed_by',
        'reversed_at',
        'reversal_of_id',
        'posted_at',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'status' => JournalEntryStatus::class,
            'approved_at' => 'datetime',
            'reversed_at' => 'datetime',
            'posted_at' => 'datetime',
        ];
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalLine::class)->orderBy('line_order');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function reverser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reversed_by');
    }

    public function originalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'reversal_of_id');
    }

    public function reversalEntry(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'reversal_of_id');
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function getTotalDebitsAttribute(): float
    {
        return (float) $this->lines->sum('debit');
    }

    public function getTotalCreditsAttribute(): float
    {
        return (float) $this->lines->sum('credit');
    }

    public function isBalanced(): bool
    {
        return round($this->total_debits, 2) === round($this->total_credits, 2);
    }

    public function scopePostedOrApproved($query)
    {
        return $query->whereIn('status', [
            JournalEntryStatus::Posted->value,
            JournalEntryStatus::Approved->value,
        ]);
    }
}
