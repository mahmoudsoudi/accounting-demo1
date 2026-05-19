<?php

namespace App\Services\Accounting;

use App\Models\Account;
use App\Models\JournalEntryLine;
use Carbon\Carbon;

class ReportService
{
    /**
     * Get trial balance
     */
    public function trialBalance(?string $from = null, ?string $to = null): array
    {
        $query = JournalEntryLine::whereHas('journalEntry', function ($q) {
            $q->where('status', 'posted');
        });

        if ($from) {
            $query->whereHas('journalEntry', function ($q) use ($from) {
                $q->where('date', '>=', $from);
            });
        }

        if ($to) {
            $query->whereHas('journalEntry', function ($q) use ($to) {
                $q->where('date', '<=', $to);
            });
        }

        $lines = $query->get();

        $rows = [];
        $totalDebit = 0;
        $totalCredit = 0;

        $accounts = Account::all();
        foreach ($accounts as $account) {
            $accountLines = $lines->where('account_id', $account->id);

            $debitTotal = $accountLines->sum('debit');
            $creditTotal = $accountLines->sum('credit');

            // Calculate balance based on account type
            $debitBalance = 0;
            $creditBalance = 0;

            if (in_array($account->type, ['asset', 'expense'])) {
                $debitBalance = $debitTotal - $creditTotal;
            } else {
                $creditBalance = $creditTotal - $debitTotal;
            }

            if ($debitBalance != 0 || $creditBalance != 0) {
                $rows[] = [
                    'account' => $account,
                    'debit_total' => number_format($debitTotal, 2),
                    'credit_total' => number_format($creditTotal, 2),
                    'debit_balance' => $debitBalance > 0 ? number_format($debitBalance, 2) : '0.00',
                    'credit_balance' => $creditBalance > 0 ? number_format($creditBalance, 2) : '0.00',
                ];

                $totalDebit += $debitTotal;
                $totalCredit += $creditTotal;
            }
        }

        usort($rows, function ($a, $b) {
            return strcmp($a['account']->code, $b['account']->code);
        });

        return [
            'rows' => $rows,
            'total_debit' => number_format($totalDebit, 2),
            'total_credit' => number_format($totalCredit, 2),
            'is_balanced' => round($totalDebit, 2) === round($totalCredit, 2),
        ];
    }

    /**
     * Get general ledger for an account
     */
    public function generalLedger(int $accountId, ?string $from = null, ?string $to = null): array
    {
        $account = Account::findOrFail($accountId);

        $query = JournalEntryLine::where('account_id', $accountId)
            ->whereHas('journalEntry', function ($q) {
                $q->where('status', 'posted');
            });

        if ($from) {
            $query->whereHas('journalEntry', function ($q) use ($from) {
                $q->where('date', '>=', $from);
            });
        }

        if ($to) {
            $query->whereHas('journalEntry', function ($q) use ($to) {
                $q->where('date', '<=', $to);
            });
        }

        $lines = $query->with('journalEntry')
            ->orderBy('created_at')
            ->get();

        $runningBalance = 0;
        $ledgerLines = [];

        foreach ($lines as $line) {
            if (in_array($account->type, ['asset', 'expense'])) {
                $runningBalance += $line->debit - $line->credit;
            } else {
                $runningBalance += $line->credit - $line->debit;
            }

            $ledgerLines[] = [
                'date' => $line->journalEntry->date->format('Y-m-d'),
                'entry_number' => $line->journalEntry->entry_number,
                'description' => $line->journalEntry->description ?? $line->description,
                'debit' => number_format($line->debit, 2),
                'credit' => number_format($line->credit, 2),
                'running_balance' => number_format($runningBalance, 2),
            ];
        }

        return [
            'account' => $account,
            'opening_balance' => '0.00',
            'lines' => $ledgerLines,
            'closing_balance' => number_format($runningBalance, 2),
        ];
    }

    /**
     * Get profit and loss report
     */
    public function profitLoss(?string $from = null, ?string $to = null): array
    {
        $query = JournalEntryLine::whereHas('journalEntry', function ($q) {
            $q->where('status', 'posted');
        });

        if ($from) {
            $query->whereHas('journalEntry', function ($q) use ($from) {
                $q->where('date', '>=', $from);
            });
        }

        if ($to) {
            $query->whereHas('journalEntry', function ($q) use ($to) {
                $q->where('date', '<=', $to);
            });
        }

        $lines = $query->get();

        $revenues = [];
        $expenses = [];
        $totalRevenue = 0;
        $totalExpense = 0;

        $revenueAccounts = Account::where('type', 'revenue')->get();
        foreach ($revenueAccounts as $account) {
            $accountLines = $lines->where('account_id', $account->id);
            $balance = $accountLines->sum('credit') - $accountLines->sum('debit');

            if ($balance != 0) {
                $revenues[] = [
                    'account' => $account,
                    'amount' => number_format($balance, 2),
                ];
                $totalRevenue += $balance;
            }
        }

        $expenseAccounts = Account::where('type', 'expense')->get();
        foreach ($expenseAccounts as $account) {
            $accountLines = $lines->where('account_id', $account->id);
            $balance = $accountLines->sum('debit') - $accountLines->sum('credit');

            if ($balance != 0) {
                $expenses[] = [
                    'account' => $account,
                    'amount' => number_format($balance, 2),
                ];
                $totalExpense += $balance;
            }
        }

        $netProfit = $totalRevenue - $totalExpense;

        return [
            'revenues' => $revenues,
            'expenses' => $expenses,
            'total_revenue' => number_format($totalRevenue, 2),
            'total_expense' => number_format($totalExpense, 2),
            'net_profit' => number_format($netProfit, 2),
        ];
    }

    /**
     * Get balance sheet
     */
    public function balanceSheet(?string $to = null): array
    {
        if (!$to) {
            $to = now()->toDateString();
        }

        $query = JournalEntryLine::whereHas('journalEntry', function ($q) use ($to) {
            $q->where('status', 'posted')
                ->where('date', '<=', $to);
        });

        $lines = $query->get();

        $assets = [];
        $liabilities = [];
        $equity = [];

        $totalAssets = 0;
        $totalLiabilities = 0;
        $totalEquity = 0;

        $assetAccounts = Account::where('type', 'asset')->get();
        foreach ($assetAccounts as $account) {
            $accountLines = $lines->where('account_id', $account->id);
            $balance = $accountLines->sum('debit') - $accountLines->sum('credit');

            if ($balance != 0) {
                $assets[] = [
                    'account' => $account,
                    'amount' => number_format($balance, 2),
                ];
                $totalAssets += $balance;
            }
        }

        $liabilityAccounts = Account::where('type', 'liability')->get();
        foreach ($liabilityAccounts as $account) {
            $accountLines = $lines->where('account_id', $account->id);
            $balance = $accountLines->sum('credit') - $accountLines->sum('debit');

            if ($balance != 0) {
                $liabilities[] = [
                    'account' => $account,
                    'amount' => number_format($balance, 2),
                ];
                $totalLiabilities += $balance;
            }
        }

        $equityAccounts = Account::where('type', 'equity')->get();
        foreach ($equityAccounts as $account) {
            $accountLines = $lines->where('account_id', $account->id);
            $balance = $accountLines->sum('credit') - $accountLines->sum('debit');

            if ($balance != 0) {
                $equity[] = [
                    'account' => $account,
                    'amount' => number_format($balance, 2),
                ];
                $totalEquity += $balance;
            }
        }

        // Calculate current earnings (P&L)
        $currentEarnings = $this->profitLoss(null, $to);
        $netProfit = (float) $currentEarnings['net_profit'];
        $totalEquity += $netProfit;

        $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;

        return [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'current_earnings' => number_format($netProfit, 2),
            'total_assets' => number_format($totalAssets, 2),
            'total_liabilities' => number_format($totalLiabilities, 2),
            'total_equity' => number_format($totalEquity, 2),
            'total_liabilities_and_equity' => number_format($totalLiabilitiesAndEquity, 2),
            'is_balanced' => abs($totalAssets - $totalLiabilitiesAndEquity) < 0.01,
        ];
    }
}
