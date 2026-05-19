<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Accounting\StoreAccountRequest;
use App\Http\Requests\Accounting\UpdateAccountRequest;
use App\Models\Account;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::orderBy('code')->get();
        return view('accounting.accounts.index', compact('accounts'));
    }

    public function create()
    {
        $types = ['asset', 'liability', 'equity', 'revenue', 'expense'];
        $parents = Account::whereNull('parent_id')->get();
        return view('accounting.accounts.create', compact('types', 'parents'));
    }

    public function store(StoreAccountRequest $request)
    {
        $data = $request->validated();
        $data['normal_balance'] = $this->getNormalBalance($data['type']);
        $data['is_active'] = true;

        Account::create($data);

        return redirect()->route('accounting.accounts.index')
            ->with('success', 'Account created successfully.');
    }

    public function show(Account $account)
    {
        $account->load('journalLines.journalEntry');
        return view('accounting.accounts.show', compact('account'));
    }

    public function edit(Account $account)
    {
        $types = ['asset', 'liability', 'equity', 'revenue', 'expense'];
        $parents = Account::whereNull('parent_id')->where('id', '!=', $account->id)->get();
        return view('accounting.accounts.edit', compact('account', 'types', 'parents'));
    }

    public function update(UpdateAccountRequest $request, Account $account)
    {
        $data = $request->validated();
        $data['normal_balance'] = $this->getNormalBalance($data['type']);

        if (!isset($data['is_active'])) {
            $data['is_active'] = false;
        }

        $account->update($data);

        return redirect()->route('accounting.accounts.show', $account)
            ->with('success', 'Account updated successfully.');
    }

    public function destroy(Account $account)
    {
        // Don't hard delete if it has journal lines
        if ($account->journalLines()->exists()) {
            $account->update(['is_active' => false]);
            return redirect()->route('accounting.accounts.index')
                ->with('success', 'Account deactivated successfully.');
        }

        $account->delete();
        return redirect()->route('accounting.accounts.index')
            ->with('success', 'Account deleted successfully.');
    }

    protected function getNormalBalance(string $type): string
    {
        return match ($type) {
            'asset', 'expense' => 'debit',
            'liability', 'equity', 'revenue' => 'credit',
        };
    }
}
