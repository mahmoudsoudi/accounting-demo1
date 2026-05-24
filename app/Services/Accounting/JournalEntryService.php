<?php

namespace App\Services\Accounting;

use App\Enums\JournalEntryStatus;
use App\Models\AuditLog;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class JournalEntryService
{
    public function create(array $data, User $user): JournalEntry
    {
        $this->assertLinesBalanced($data['lines'] ?? []);

        return DB::transaction(function () use ($data, $user) {
            $entry = JournalEntry::create([
                'reference_number' => $data['reference_number'] ?? $this->generateReference(),
                'transaction_date' => $data['transaction_date'],
                'status' => JournalEntryStatus::Draft,
                'notes' => $data['notes'] ?? null,
                'attachment_path' => $data['attachment_path'] ?? null,
                'created_by' => $user->id,
            ]);

            $this->associateReferenceable($entry, $data['referenceable'] ?? null);
            $this->syncLines($entry, $data['lines'] ?? []);
            $this->logAudit($entry, $user, 'created', null, $entry->toArray());

            return $entry->load('lines.account');
        });
    }

    public function update(JournalEntry $entry, array $data, User $user): JournalEntry
    {
        if ($entry->status !== JournalEntryStatus::Draft) {
            throw new InvalidArgumentException(__('Only draft journal entries can be edited.'));
        }

        return DB::transaction(function () use ($entry, $data, $user) {
            $old = $entry->toArray();

            $entry->update([
                'transaction_date' => $data['transaction_date'] ?? $entry->transaction_date,
                'notes' => $data['notes'] ?? $entry->notes,
                'attachment_path' => $data['attachment_path'] ?? $entry->attachment_path,
            ]);

            if (isset($data['lines'])) {
                $this->assertLinesBalanced($data['lines']);
                $this->syncLines($entry, $data['lines']);
            }

            $this->logAudit($entry, $user, 'updated', $old, $entry->fresh()->toArray());

            return $entry->load('lines.account');
        });
    }

    public function post(JournalEntry $entry, User $user): JournalEntry
    {
        $entry->load('lines');

        if (! $entry->isBalanced()) {
            throw new InvalidArgumentException(__('Journal entry must balance before posting.'));
        }

        if ($entry->lines->count() < 2) {
            throw new InvalidArgumentException(__('Journal entry must have at least two line items.'));
        }

        $entry->update([
            'status' => JournalEntryStatus::Posted,
            'posted_at' => now(),
        ]);

        $this->logAudit($entry, $user, 'posted', null, $entry->toArray());

        return $entry;
    }

    public function approve(JournalEntry $entry, User $user): JournalEntry
    {
        if (! in_array($entry->status, [JournalEntryStatus::Posted, JournalEntryStatus::Draft])) {
            throw new InvalidArgumentException(__('Only posted or draft entries can be approved.'));
        }

        if ($entry->status === JournalEntryStatus::Draft) {
            $this->post($entry, $user);
        }

        $entry->update([
            'status' => JournalEntryStatus::Approved,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        $this->logAudit($entry, $user, 'approved', null, $entry->toArray());

        return $entry;
    }

    public function reverse(JournalEntry $entry, User $user, ?string $notes = null): JournalEntry
    {
        if ($entry->status !== JournalEntryStatus::Approved) {
            throw new InvalidArgumentException(__('Only approved journal entries can be reversed.'));
        }

        return DB::transaction(function () use ($entry, $user, $notes) {
            $entry->load('lines');

            $reversal = JournalEntry::create([
                'reference_number' => $this->generateReference('REV'),
                'transaction_date' => now()->toDateString(),
                'status' => JournalEntryStatus::Approved,
                'notes' => $notes ?? __('Reversal of :ref', ['ref' => $entry->reference_number]),
                'created_by' => $user->id,
                'approved_by' => $user->id,
                'approved_at' => now(),
                'posted_at' => now(),
                'reversal_of_id' => $entry->id,
                'referenceable_type' => $entry->referenceable_type,
                'referenceable_id' => $entry->referenceable_id,
            ]);

            foreach ($entry->lines as $index => $line) {
                JournalLine::create([
                    'journal_entry_id' => $reversal->id,
                    'account_id' => $line->account_id,
                    'description' => __('Reversal: :desc', ['desc' => $line->description]),
                    'debit' => $line->credit,
                    'credit' => $line->debit,
                    'line_order' => $index + 1,
                    'referenceable_type' => $line->referenceable_type,
                    'referenceable_id' => $line->referenceable_id,
                ]);
            }

            $entry->update([
                'status' => JournalEntryStatus::Reversed,
                'reversed_by' => $user->id,
                'reversed_at' => now(),
            ]);

            $this->logAudit($entry, $user, 'reversed', null, ['reversal_id' => $reversal->id]);
            $this->logAudit($reversal, $user, 'created_as_reversal', null, ['original_id' => $entry->id]);

            return $reversal->load('lines.account', 'originalEntry');
        });
    }

    protected function assertLinesBalanced(array $lines): void
    {
        $totalDebits = 0.0;
        $totalCredits = 0.0;
        $linesWithAmounts = 0;

        foreach ($lines as $line) {
            if (empty($line['account_id'])) {
                continue;
            }

            $debit = (float) ($line['debit'] ?? 0);
            $credit = (float) ($line['credit'] ?? 0);

            $totalDebits += $debit;
            $totalCredits += $credit;

            if ($debit > 0 || $credit > 0) {
                $linesWithAmounts++;
            }
        }

        if (round($totalDebits, 2) <= 0 && round($totalCredits, 2) <= 0) {
            throw new InvalidArgumentException(__('Journal entry total must be greater than zero.'));
        }

        if ($linesWithAmounts < 2) {
            throw new InvalidArgumentException(__('Journal entry must have at least two line items with amounts.'));
        }

        if (round($totalDebits, 2) !== round($totalCredits, 2)) {
            throw new InvalidArgumentException(__('Journal entry must balance before saving. Debits (:debits) must equal credits (:credits).', [
                'debits' => number_format($totalDebits, 2),
                'credits' => number_format($totalCredits, 2),
            ]));
        }
    }

    protected function syncLines(JournalEntry $entry, array $lines): void
    {
        $entry->lines()->delete();

        foreach ($lines as $index => $line) {
            if (empty($line['account_id'])) {
                continue;
            }

            JournalLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $line['account_id'],
                'description' => $line['description'] ?? null,
                'debit' => (float) ($line['debit'] ?? 0),
                'credit' => (float) ($line['credit'] ?? 0),
                'line_order' => $index + 1,
                'referenceable_type' => $this->resolveReferenceableType($line['referenceable'] ?? null),
                'referenceable_id' => $this->resolveReferenceableId($line['referenceable'] ?? null),
            ]);
        }
    }

    protected function associateReferenceable(JournalEntry $entry, mixed $referenceable): void
    {
        if (! $referenceable instanceof Model) {
            return;
        }

        $entry->referenceable()->associate($referenceable);
        $entry->save();
    }

    protected function resolveReferenceableType(mixed $referenceable): ?string
    {
        return $referenceable instanceof Model ? $referenceable->getMorphClass() : null;
    }

    protected function resolveReferenceableId(mixed $referenceable): ?int
    {
        return $referenceable instanceof Model ? $referenceable->getKey() : null;
    }

    protected function generateReference(string $prefix = 'JE'): string
    {
        return $prefix.'-'.now()->format('Ymd').'-'.str_pad((string) (JournalEntry::count() + 1), 4, '0', STR_PAD_LEFT);
    }

    protected function logAudit(JournalEntry $entry, User $user, string $action, ?array $old, ?array $new): void
    {
        AuditLog::create([
            'auditable_type' => JournalEntry::class,
            'auditable_id' => $entry->id,
            'user_id' => $user->id,
            'action' => $action,
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}
