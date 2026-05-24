<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Agent;
use App\Models\Client;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\Payment;
use App\Models\Property;
use App\Services\Accounting\FinancialStatementService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected FinancialStatementService $financials
    ) {}

    public function index(Request $request): View
    {
        $selectedYear = (int) $request->query('year', now()->year);
        $currentYear = now()->year;

        if ($selectedYear < 2000 || $selectedYear > $currentYear + 1) {
            $selectedYear = $currentYear;
        }

        $startDate = "{$selectedYear}-01-01";
        $endDate = $selectedYear === $currentYear
            ? now()->toDateString()
            : "{$selectedYear}-12-31";

        $income = $this->financials->incomeStatement($startDate, $endDate);
        $balance = $this->financials->balanceSheet($endDate);
        $cashFlow = $this->financials->cashFlow($startDate, $endDate);

        $availableYears = JournalEntry::query()
            ->selectRaw('YEAR(transaction_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn ($year) => (int) $year)
            ->push($currentYear)
            ->unique()
            ->sortDesc()
            ->values();

        return view('dashboard.index', [
            'selectedYear' => $selectedYear,
            'endDate' => $endDate,
            'availableYears' => $availableYears,
            'totalRevenue' => $income['total_revenue'],
            'netIncome' => $income['net_income'],
            'totalAssets' => $balance['total_assets'],
            'totalLiabilities' => $balance['total_liabilities'],
            'netCashFlow' => $cashFlow['net_cash_flow'],
            'pendingExpenses' => Expense::where('status', 'pending')->count(),
            'openInvoices' => Invoice::whereIn('status', ['sent', 'partial', 'overdue'])->count(),
            'draftJournals' => JournalEntry::where('status', 'draft')->count(),
            'recentJournals' => JournalEntry::with('creator')
                ->whereYear('transaction_date', $selectedYear)
                ->latest()
                ->take(5)
                ->get(),
            'clientCount' => Client::count(),
            'agentCount' => Agent::count(),
            'propertyCount' => Property::count(),
            'accountCount' => Account::active()->count(),
            'paymentCount' => Payment::count(),
        ]);
    }
}
