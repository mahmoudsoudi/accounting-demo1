<?php

namespace App\Services\Accounting;

use App\Enums\AccountType;
use App\Enums\JournalEntryStatus;
use App\Enums\NormalBalance;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FinancialStatementService
{
    public function trialBalance(?string $asOfDate = null): Collection
    {
        $balances = $this->accountBalances($asOfDate);

        return $balances->map(function ($row) {
            $balance = $row['balance'];

            return [
                'account' => $row['account'],
                'debit' => $balance > 0 && $row['account']->normal_balance === NormalBalance::Debit ? abs($balance) : 0,
                'credit' => $balance > 0 && $row['account']->normal_balance === NormalBalance::Credit ? abs($balance) : ($balance < 0 ? abs($balance) : 0),
            ];
        });
    }

    public function balanceSheet(?string $asOfDate = null): array
    {
        $balances = $this->accountBalances($asOfDate);

        return [
            'assets' => $this->filterByType($balances, AccountType::Asset),
            'liabilities' => $this->filterByType($balances, AccountType::Liability),
            'equity' => $this->filterByType($balances, AccountType::Equity),
            'total_assets' => $this->sumType($balances, AccountType::Asset),
            'total_liabilities' => $this->sumType($balances, AccountType::Liability),
            'total_equity' => $this->sumType($balances, AccountType::Equity),
        ];
    }

    public function incomeStatement(?string $startDate = null, ?string $endDate = null): array
    {
        $balances = $this->accountBalances($endDate, $startDate);

        return [
            'revenue' => $this->filterByType($balances, AccountType::Revenue),
            'cost_of_sales' => $this->filterByType($balances, AccountType::CostOfSales),
            'expenses' => $this->filterByType($balances, AccountType::Expense),
            'total_revenue' => $this->sumType($balances, AccountType::Revenue),
            'total_cost_of_sales' => $this->sumType($balances, AccountType::CostOfSales),
            'total_expenses' => $this->sumType($balances, AccountType::Expense),
            'net_income' => $this->sumType($balances, AccountType::Revenue)
                - $this->sumType($balances, AccountType::CostOfSales)
                - $this->sumType($balances, AccountType::Expense),
        ];
    }

    public function generalLedger(int $accountId, ?string $startDate = null, ?string $endDate = null): Collection
    {
        $query = JournalLine::query()
            ->with(['journalEntry', 'account'])
            ->where('account_id', $accountId)
            ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                $q->whereIn('status', [JournalEntryStatus::Posted->value, JournalEntryStatus::Approved->value]);
                if ($startDate) {
                    $q->whereDate('transaction_date', '>=', $startDate);
                }
                if ($endDate) {
                    $q->whereDate('transaction_date', '<=', $endDate);
                }
            })
            ->join('journal_entries', 'journal_lines.journal_entry_id', '=', 'journal_entries.id')
            ->orderBy('journal_entries.transaction_date')
            ->orderBy('journal_lines.id')
            ->select('journal_lines.*');

        $running = 0;
        $account = Account::findOrFail($accountId);

        return $query->get()->map(function (JournalLine $line) use (&$running, $account) {
            $change = $account->normal_balance === NormalBalance::Debit
                ? (float) $line->debit - (float) $line->credit
                : (float) $line->credit - (float) $line->debit;
            $running += $change;

            return [
                'line' => $line,
                'running_balance' => $running,
            ];
        });
    }

    /**
     * @param  array<int>  $accountIds
     */
    public function generalLedgers(array $accountIds, ?string $startDate = null, ?string $endDate = null): Collection
    {
        if ($accountIds === []) {
            return collect();
        }

        return Account::query()
            ->whereIn('id', $accountIds)
            ->orderBy('code')
            ->get()
            ->mapWithKeys(function (Account $account) use ($startDate, $endDate) {
                return [
                    $account->id => [
                        'account' => $account,
                        'rows' => $this->generalLedger($account->id, $startDate, $endDate),
                    ],
                ];
            })
            ->filter(fn (array $section) => $section['rows']->isNotEmpty());
    }

    public function cashFlow(?string $startDate = null, ?string $endDate = null): array
    {
        $cashAccounts = Account::whereIn('code', ['1010', '1020', '1040'])->pluck('id');

        $lines = JournalLine::query()
            ->whereIn('account_id', $cashAccounts)
            ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                $q->whereIn('status', [JournalEntryStatus::Posted->value, JournalEntryStatus::Approved->value]);
                if ($startDate) {
                    $q->whereDate('transaction_date', '>=', $startDate);
                }
                if ($endDate) {
                    $q->whereDate('transaction_date', '<=', $endDate);
                }
            })
            ->get();

        $inflows = $lines->sum('debit');
        $outflows = $lines->sum('credit');

        return [
            'inflows' => $inflows,
            'outflows' => $outflows,
            'net_cash_flow' => $inflows - $outflows,
        ];
    }

    public function accountsReceivableAging(?string $asOfDate = null): Collection
    {
        return DB::table('invoices')
            ->whereIn('status', ['sent', 'partial', 'overdue'])
            ->selectRaw('
                CASE
                    WHEN DATEDIFF(?, due_date) <= 0 THEN "current"
                    WHEN DATEDIFF(?, due_date) BETWEEN 1 AND 30 THEN "1-30"
                    WHEN DATEDIFF(?, due_date) BETWEEN 31 AND 60 THEN "31-60"
                    WHEN DATEDIFF(?, due_date) BETWEEN 61 AND 90 THEN "61-90"
                    ELSE "90+"
                END as bucket,
                SUM(total) as total
            ', [$asOfDate ?? now()->toDateString(), $asOfDate ?? now()->toDateString(), $asOfDate ?? now()->toDateString(), $asOfDate ?? now()->toDateString()])
            ->groupBy('bucket')
            ->get();
    }

    public function journalRegister(?string $startDate = null, ?string $endDate = null): Collection
    {
        $query = JournalEntry::with(['lines.account', 'creator'])
            ->whereIn('status', [JournalEntryStatus::Posted->value, JournalEntryStatus::Approved->value, JournalEntryStatus::Reversed->value]);

        if ($startDate) {
            $query->whereDate('transaction_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }

        return $query->orderByDesc('transaction_date')->get();
    }

    protected function accountBalances(?string $asOfDate = null, ?string $startDate = null): Collection
    {
        $accounts = Account::active()->orderBy('code')->get();

        return $accounts->map(function (Account $account) use ($asOfDate, $startDate) {
            $query = JournalLine::query()
                ->where('account_id', $account->id)
                ->whereHas('journalEntry', function ($q) use ($asOfDate, $startDate) {
                    $q->whereIn('status', [JournalEntryStatus::Posted->value, JournalEntryStatus::Approved->value]);
                    if ($asOfDate) {
                        $q->whereDate('transaction_date', '<=', $asOfDate);
                    }
                    if ($startDate) {
                        $q->whereDate('transaction_date', '>=', $startDate);
                    }
                });

            $debits = (float) (clone $query)->sum('debit');
            $credits = (float) (clone $query)->sum('credit');

            $balance = $account->normal_balance === NormalBalance::Debit
                ? $debits - $credits
                : $credits - $debits;

            return [
                'account' => $account,
                'debits' => $debits,
                'credits' => $credits,
                'balance' => $balance,
            ];
        })->filter(fn ($row) => abs($row['balance']) > 0.001 || abs($row['debits']) > 0 || abs($row['credits']) > 0);
    }

    protected function filterByType(Collection $balances, AccountType $type): Collection
    {
        return $balances->filter(fn ($row) => $row['account']->account_type === $type)->values();
    }

    protected function sumType(Collection $balances, AccountType $type): float
    {
        return (float) $this->filterByType($balances, $type)->sum('balance');
    }
}
