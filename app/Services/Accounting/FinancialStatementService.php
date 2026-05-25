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
    /** @var list<string> */
    private const INTEREST_EXPENSE_CODES = ['6070'];

    /** @var list<string> */
    private const DEPRECIATION_EXPENSE_CODES = ['6110'];

    /** @var list<string> */
    private const AMORTIZATION_EXPENSE_CODES = ['6115'];

    /** @var list<string> */
    private const INCOME_TAX_EXPENSE_CODES = ['6120'];

    /** @var list<string> */
    private const TRUST_ACCOUNT_CODES = ['1030', '2030'];

    public function trialBalance(?string $endDate = null, ?string $startDate = null): Collection
    {
        return $this->accountBalances($endDate, $startDate)
            ->map(fn (array $row) => $this->mapTrialBalanceRow($row));
    }

    /**
     * @return array<int, array{debit: float, credit: float, balance: float}>
     */
    public function trialBalanceAmountsByAccountId(?string $endDate = null, ?string $startDate = null): array
    {
        return $this->accountBalances($endDate, $startDate, activeOnly: false, hideZeroBalances: false)
            ->mapWithKeys(function (array $row) {
                $mapped = $this->mapTrialBalanceRow($row);

                return [
                    $row['account']->id => [
                        'debit' => $mapped['debit'],
                        'credit' => $mapped['credit'],
                        'balance' => $row['balance'],
                    ],
                ];
            })
            ->all();
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

        $revenue = $this->filterByType($balances, AccountType::Revenue);
        $costOfSales = $this->filterByType($balances, AccountType::CostOfSales);
        $allExpenses = $this->filterByType($balances, AccountType::Expense);

        $belowEbitdaCodes = array_merge(
            self::INTEREST_EXPENSE_CODES,
            self::DEPRECIATION_EXPENSE_CODES,
            self::AMORTIZATION_EXPENSE_CODES,
            self::INCOME_TAX_EXPENSE_CODES,
        );

        $operatingExpenses = $allExpenses->filter(
            fn ($row) => ! in_array($row['account']->code, $belowEbitdaCodes, true)
        )->values();
        $interestExpense = $this->filterByAccountCodes($allExpenses, self::INTEREST_EXPENSE_CODES);
        $depreciationExpense = $this->filterByAccountCodes($allExpenses, self::DEPRECIATION_EXPENSE_CODES);
        $amortizationExpense = $this->filterByAccountCodes($allExpenses, self::AMORTIZATION_EXPENSE_CODES);
        $incomeTaxExpense = $this->filterByAccountCodes($allExpenses, self::INCOME_TAX_EXPENSE_CODES);

        $totalRevenue = $this->sumCollection($revenue);
        $totalCostOfSales = $this->sumCollection($costOfSales);
        $totalOperatingExpenses = $this->sumCollection($operatingExpenses);
        $totalInterestExpense = $this->sumCollection($interestExpense);
        $totalDepreciationExpense = $this->sumCollection($depreciationExpense);
        $totalAmortizationExpense = $this->sumCollection($amortizationExpense);
        $totalIncomeTaxExpense = $this->sumCollection($incomeTaxExpense);

        $grossProfit = $totalRevenue - $totalCostOfSales;
        $ebitda = $grossProfit - $totalOperatingExpenses;
        $ebit = $ebitda - $totalDepreciationExpense - $totalAmortizationExpense;
        $ebt = $ebit - $totalInterestExpense;
        $netIncomeAfterTax = $ebt - $totalIncomeTaxExpense;

        return [
            'revenue' => $revenue,
            'cost_of_sales' => $costOfSales,
            'expenses' => $operatingExpenses,
            'interest_expense' => $interestExpense,
            'depreciation_expense' => $depreciationExpense,
            'amortization_expense' => $amortizationExpense,
            'income_tax_expense' => $incomeTaxExpense,
            'total_revenue' => $totalRevenue,
            'total_cost_of_sales' => $totalCostOfSales,
            'gross_profit' => $grossProfit,
            'total_expenses' => $totalOperatingExpenses,
            'total_interest_expense' => $totalInterestExpense,
            'total_depreciation_expense' => $totalDepreciationExpense,
            'total_amortization_expense' => $totalAmortizationExpense,
            'total_income_tax_expense' => $totalIncomeTaxExpense,
            'ebitda' => $ebitda,
            'ebit' => $ebit,
            'ebt' => $ebt,
            'net_income_before_tax' => $ebt,
            'net_income_after_tax' => $netIncomeAfterTax,
            'net_income' => $netIncomeAfterTax,
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

    public function trustFundReport(?string $startDate = null, ?string $endDate = null): array
    {
        $trustAccounts = Account::query()
            ->whereIn('code', self::TRUST_ACCOUNT_CODES)
            ->orderBy('code')
            ->get();

        $trustAsset = $trustAccounts->firstWhere('code', '1030');
        $trustLiability = $trustAccounts->firstWhere('code', '2030');

        $assetBalance = $trustAsset ? $this->accountBalance($trustAsset->id, $endDate) : 0.0;
        $liabilityBalance = $trustLiability ? $this->accountBalance($trustLiability->id, $endDate) : 0.0;

        $openingAsset = $trustAsset && $startDate
            ? $this->accountBalance($trustAsset->id, date('Y-m-d', strtotime($startDate.' -1 day')))
            : ($trustAsset ? $this->accountBalance($trustAsset->id) : 0.0);
        $openingLiability = $trustLiability && $startDate
            ? $this->accountBalance($trustLiability->id, date('Y-m-d', strtotime($startDate.' -1 day')))
            : ($trustLiability ? $this->accountBalance($trustLiability->id) : 0.0);

        $accountIds = $trustAccounts->pluck('id')->all();

        return [
            'trust_asset' => $trustAsset,
            'trust_liability' => $trustLiability,
            'asset_balance' => $assetBalance,
            'liability_balance' => $liabilityBalance,
            'opening_asset_balance' => $openingAsset,
            'opening_liability_balance' => $openingLiability,
            'shortfall_excess' => $assetBalance - $liabilityBalance,
            'ledgers' => $accountIds
                ? $this->generalLedgers($accountIds, $startDate, $endDate)
                : collect(),
        ];
    }

    public function accountBalance(int $accountId, ?string $asOfDate = null): float
    {
        $account = Account::findOrFail($accountId);

        $query = JournalLine::query()
            ->where('account_id', $account->id)
            ->whereHas('journalEntry', function ($q) use ($asOfDate) {
                $q->whereIn('status', [JournalEntryStatus::Posted->value, JournalEntryStatus::Approved->value]);
                if ($asOfDate) {
                    $q->whereDate('transaction_date', '<=', $asOfDate);
                }
            });

        $debits = (float) (clone $query)->sum('debit');
        $credits = (float) (clone $query)->sum('credit');

        return $account->normal_balance === NormalBalance::Debit
            ? $debits - $credits
            : $credits - $debits;
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

    protected function accountBalances(
        ?string $asOfDate = null,
        ?string $startDate = null,
        bool $activeOnly = true,
        bool $hideZeroBalances = true,
    ): Collection {
        $accountsQuery = Account::query()->orderBy('code');

        if ($activeOnly) {
            $accountsQuery->active();
        }

        return $accountsQuery->get()->map(function (Account $account) use ($asOfDate, $startDate) {
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
        })->when(
            $hideZeroBalances,
            fn (Collection $rows) => $rows->filter(
                fn ($row) => abs($row['balance']) > 0.001 || abs($row['debits']) > 0 || abs($row['credits']) > 0
            )
        );
    }

    protected function mapTrialBalanceRow(array $row): array
    {
        $balance = $row['balance'];

        return [
            'account' => $row['account'],
            'debit' => $balance > 0 && $row['account']->normal_balance === NormalBalance::Debit ? abs($balance) : 0,
            'credit' => $balance > 0 && $row['account']->normal_balance === NormalBalance::Credit ? abs($balance) : ($balance < 0 ? abs($balance) : 0),
            'balance' => $balance,
        ];
    }

    protected function filterByType(Collection $balances, AccountType $type): Collection
    {
        return $balances->filter(fn ($row) => $row['account']->account_type === $type)->values();
    }

    protected function sumType(Collection $balances, AccountType $type): float
    {
        return (float) $this->filterByType($balances, $type)->sum('balance');
    }

    protected function sumCollection(Collection $rows): float
    {
        return (float) $rows->sum('balance');
    }

    /**
     * @param  list<string>  $codes
     */
    protected function filterByAccountCodes(Collection $balances, array $codes): Collection
    {
        return $balances->filter(fn ($row) => in_array($row['account']->code, $codes, true))->values();
    }
}
