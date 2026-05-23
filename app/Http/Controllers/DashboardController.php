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
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected FinancialStatementService $financials
    ) {}

    public function index(): View
    {
        $income = $this->financials->incomeStatement(
            now()->startOfYear()->toDateString(),
            now()->toDateString()
        );
        $balance = $this->financials->balanceSheet(now()->toDateString());
        $cashFlow = $this->financials->cashFlow(
            now()->startOfMonth()->toDateString(),
            now()->toDateString()
        );

        return view('dashboard.index', [
            'totalRevenue' => $income['total_revenue'],
            'netIncome' => $income['net_income'],
            'totalAssets' => $balance['total_assets'],
            'totalLiabilities' => $balance['total_liabilities'],
            'netCashFlow' => $cashFlow['net_cash_flow'],
            'pendingExpenses' => Expense::where('status', 'pending')->count(),
            'openInvoices' => Invoice::whereIn('status', ['sent', 'partial', 'overdue'])->count(),
            'draftJournals' => JournalEntry::where('status', 'draft')->count(),
            'recentJournals' => JournalEntry::with('creator')->latest()->take(5)->get(),
            'clientCount' => Client::count(),
            'agentCount' => Agent::count(),
            'propertyCount' => Property::count(),
            'accountCount' => Account::active()->count(),
            'paymentCount' => Payment::count(),
        ]);
    }
}
