<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Agent;
use App\Models\Client;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Property;
use App\Services\Accounting\FinancialStatementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        protected FinancialStatementService $financials
    ) {}

    public function index(): View
    {
        Gate::authorize('view-reports');

        return view('reports.index');
    }

    public function trialBalance(Request $request): View
    {
        Gate::authorize('view-reports');

        return view('reports.trial-balance', [
            'rows' => $this->financials->trialBalance($request->query('as_of')),
            'asOf' => $request->query('as_of', now()->toDateString()),
        ]);
    }

    public function balanceSheet(Request $request): View
    {
        Gate::authorize('view-reports');

        return view('reports.balance-sheet', [
            'data' => $this->financials->balanceSheet($request->query('as_of')),
            'asOf' => $request->query('as_of', now()->toDateString()),
        ]);
    }

    public function incomeStatement(Request $request): View
    {
        Gate::authorize('view-reports');

        return view('reports.income-statement', [
            'data' => $this->financials->incomeStatement(
                $request->query('start_date', now()->startOfYear()->toDateString()),
                $request->query('end_date', now()->toDateString())
            ),
            'startDate' => $request->query('start_date', now()->startOfYear()->toDateString()),
            'endDate' => $request->query('end_date', now()->toDateString()),
        ]);
    }

    public function cashFlow(Request $request): View
    {
        Gate::authorize('view-reports');

        return view('reports.cash-flow', [
            'data' => $this->financials->cashFlow($request->query('start_date'), $request->query('end_date')),
            'startDate' => $request->query('start_date', now()->startOfYear()->toDateString()),
            'endDate' => $request->query('end_date', now()->toDateString()),
        ]);
    }

    public function generalLedger(Request $request): View
    {
        Gate::authorize('view-reports');

        $accounts = Account::active()->orderBy('code')->get();
        $accountIds = collect($request->query('account_ids', []))
            ->when(
                $request->filled('account_id'),
                fn ($ids) => $ids->push($request->query('account_id'))
            )
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        return view('reports.general-ledger', [
            'accounts' => $accounts,
            'accountIds' => $accountIds,
            'ledgers' => $accountIds
                ? $this->financials->generalLedgers(
                    $accountIds,
                    $request->query('start_date'),
                    $request->query('end_date')
                )
                : collect(),
            'startDate' => $request->query('start_date'),
            'endDate' => $request->query('end_date'),
        ]);
    }

    public function journalRegister(Request $request): View
    {
        Gate::authorize('view-reports');

        return view('reports.journal-register', [
            'entries' => $this->financials->journalRegister($request->query('start_date'), $request->query('end_date')),
            'startDate' => $request->query('start_date'),
            'endDate' => $request->query('end_date'),
        ]);
    }

    public function arAging(): View
    {
        Gate::authorize('view-reports');

        return view('reports.ar-aging', [
            'rows' => $this->financials->accountsReceivableAging(),
        ]);
    }

    public function expenseSummary(): View
    {
        Gate::authorize('view-reports');

        $summary = Expense::selectRaw('category, SUM(total) as total')
            ->where('status', 'approved')
            ->groupBy('category')
            ->get();

        return view('reports.expense-summary', compact('summary'));
    }

    public function revenueByDimension(): View
    {
        Gate::authorize('view-reports');

        return view('reports.revenue-by-dimension', [
            'byClient' => Invoice::selectRaw('client_id, SUM(total) as total')->groupBy('client_id')->with('client')->get(),
            'byProperty' => Invoice::selectRaw('property_id, SUM(total) as total')->groupBy('property_id')->with('property')->get(),
            'byAgent' => Invoice::selectRaw('agent_id, SUM(total) as total')->groupBy('agent_id')->with('agent')->get(),
        ]);
    }
}
