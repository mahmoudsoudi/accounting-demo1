<?php

namespace App\Services\Accounting;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class JournalEntryService
{
    /**
     * Create a new journal entry (draft or posted)
     */
    public function create(array $data, bool $post = false): JournalEntry
    {
        return DB::transaction(function () use ($data, $post) {
            $this->validateLines($data['lines'] ?? []);

            $journalEntry = JournalEntry::create([
                'entry_number' => $this->generateEntryNumber(),
                'date' => $data['date'],
                'description' => $data['description'] ?? null,
                'status' => $post ? 'posted' : 'draft',
                'posted_at' => $post ? now() : null,
                'created_by' => auth()->id(),
            ]);

            $lines = $data['lines'] ?? [];
            foreach ($lines as $lineData) {
                if ((float) ($lineData['debit'] ?? 0) > 0 || (float) ($lineData['credit'] ?? 0) > 0) {
                    JournalEntryLine::create([
                        'journal_entry_id' => $journalEntry->id,
                        'account_id' => $lineData['account_id'],
                        'description' => $lineData['description'] ?? null,
                        'debit' => $lineData['debit'] ?? 0,
                        'credit' => $lineData['credit'] ?? 0,
                    ]);
                }
            }

            return $journalEntry;
        });
    }

    /**
     * Post a draft journal entry
     */
    public function post(JournalEntry $journalEntry): JournalEntry
    {
        return DB::transaction(function () use ($journalEntry) {
            if ($journalEntry->status !== 'draft') {
                throw ValidationException::withMessages([
                    'status' => 'Only draft entries can be posted.',
                ]);
            }

            $journalEntry->load('lines');
            $this->validateLines($journalEntry->lines->toArray());

            $journalEntry->update([
                'status' => 'posted',
                'posted_at' => now(),
            ]);

            return $journalEntry;
        });
    }

    /**
     * Reverse a posted journal entry
     */
    public function reverse(JournalEntry $journalEntry): JournalEntry
    {
        return DB::transaction(function () use ($journalEntry) {
            if ($journalEntry->status !== 'posted') {
                throw ValidationException::withMessages([
                    'status' => 'Only posted entries can be reversed.',
                ]);
            }

            if ($journalEntry->reversed_at) {
                throw ValidationException::withMessages([
                    'status' => 'This entry has already been reversed.',
                ]);
            }

            $reversalEntry = JournalEntry::create([
                'entry_number' => $this->generateEntryNumber(),
                'date' => now()->toDateString(),
                'description' => 'Reversal of ' . $journalEntry->entry_number,
                'status' => 'posted',
                'posted_at' => now(),
                'reversal_of_id' => $journalEntry->id,
                'created_by' => auth()->id(),
            ]);

            // Reverse each line
            foreach ($journalEntry->lines as $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $reversalEntry->id,
                    'account_id' => $line->account_id,
                    'description' => $line->description,
                    'debit' => $line->credit,
                    'credit' => $line->debit,
                ]);
            }

            // Mark original as reversed
            $journalEntry->update([
                'reversed_at' => now(),
            ]);

            return $reversalEntry;
        });
    }

    /**
     * Validate journal entry lines
     */
    protected function validateLines(array $lines): void
    {
        if (count($lines) < 2) {
            throw ValidationException::withMessages([
                'lines' => 'A journal entry must have at least 2 lines.',
            ]);
        }

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($lines as $line) {
            // Skip empty lines
            if (!isset($line['account_id']) || empty($line['account_id'])) {
                continue;
            }

            $account = Account::find($line['account_id'] ?? null);
            if (!$account) {
                throw ValidationException::withMessages([
                    'lines' => 'Invalid account selected.',
                ]);
            }

            if (!$account->is_active) {
                throw ValidationException::withMessages([
                    'lines' => "Account '{$account->code}' is inactive.",
                ]);
            }

            $debit = (float) ($line['debit'] ?? 0);
            $credit = (float) ($line['credit'] ?? 0);

            // Both cannot be non-zero
            if ($debit > 0 && $credit > 0) {
                throw ValidationException::withMessages([
                    'lines' => 'A line cannot have both debit and credit.',
                ]);
            }

            // Both cannot be zero
            if ($debit == 0 && $credit == 0) {
                throw ValidationException::withMessages([
                    'lines' => 'A line must have either debit or credit.',
                ]);
            }

            $totalDebit += $debit;
            $totalCredit += $credit;
        }

        // Check balance
        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            throw ValidationException::withMessages([
                'lines' => 'Total debit must equal total credit.',
            ]);
        }
    }

    /**
     * Generate entry number
     */
    protected function generateEntryNumber(): string
    {
        $lastEntry = JournalEntry::latest('id')->first();
        $nextId = ($lastEntry ? $lastEntry->id : 0) + 1;
        return 'JE-' . str_pad((string) $nextId, 6, '0', STR_PAD_LEFT);
    }
}
