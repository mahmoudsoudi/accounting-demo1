<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\Accounting\FinancialStatementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct(
        protected FinancialStatementService $financials
    ) {}

    public function index(): View
    {
        $accounts = Account::with('parent')->orderBy('code')->get();

        return view('accounts.index', compact('accounts'));
    }

    public function create(): View
    {
        return view('accounts.create', [
            'parents' => Account::orderBy('code')->get(),
            'types' => \App\Enums\AccountType::cases(),
            'balances' => \App\Enums\NormalBalance::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:accounts,code'],
            'name' => ['required', 'string', 'max:255'],
            'account_type' => ['required', 'string'],
            'parent_id' => ['nullable', 'exists:accounts,id'],
            'normal_balance' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        Account::create($data);

        return redirect()->route('accounts.index')->with('success', __('Account created successfully.'));
    }

    public function show(Request $request, Account $account): View
    {
        $account->load([
            'parent',
            'children' => fn ($query) => $query->orderBy('code'),
        ]);

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $rows = $this->financials->generalLedger($account->id, $startDate, $endDate);
        $currentBalance = ($startDate || $endDate)
            ? ($this->financials->generalLedger($account->id)->last()['running_balance'] ?? 0.0)
            : ($rows->last()['running_balance'] ?? 0.0);

        return view('accounts.show', [
            'account' => $account,
            'rows' => $rows,
            'currentBalance' => $currentBalance,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function edit(Account $account): View
    {
        return view('accounts.edit', [
            'account' => $account,
            'parents' => Account::where('id', '!=', $account->id)->orderBy('code')->get(),
            'types' => \App\Enums\AccountType::cases(),
            'balances' => \App\Enums\NormalBalance::cases(),
        ]);
    }

    public function update(Request $request, Account $account): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:accounts,code,'.$account->id],
            'name' => ['required', 'string', 'max:255'],
            'account_type' => ['required', 'string'],
            'parent_id' => ['nullable', 'exists:accounts,id'],
            'normal_balance' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $account->update($data);

        return redirect()->route('accounts.index')->with('success', __('Account updated successfully.'));
    }
}
