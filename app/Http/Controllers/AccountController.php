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
        $fiscalStart = now()->startOfYear()->toDateString();
        $fiscalEnd = now()->toDateString();
        $balances = $this->financials->trialBalanceAmountsByAccountId($fiscalEnd, $fiscalStart);

        return view('accounts.index', compact('accounts', 'balances', 'fiscalStart', 'fiscalEnd'));
    }

    public function create(): View
    {
        return view('accounts.create', [
            'parents' => Account::orderBy('code')->get(),
            'types' => \App\Enums\AccountType::cases(),
            'balances' => \App\Enums\NormalBalance::cases(),
            'locales' => config('locales.supported'),
            'localeLabels' => config('locales.labels'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateAccount($request);
        $data['is_active'] = $request->boolean('is_active', true);

        $account = new Account($data);
        $account->setTranslationField('name', $data['name_translations']);
        $account->setTranslationField('description', $data['description_translations'] ?? []);
        $account->save();

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
            'locales' => config('locales.supported'),
            'localeLabels' => config('locales.labels'),
        ]);
    }

    public function update(Request $request, Account $account): RedirectResponse
    {
        $data = $this->validateAccount($request, $account);
        $data['is_active'] = $request->boolean('is_active');

        $account->fill($data);
        $account->setTranslationField('name', $data['name_translations']);
        $account->setTranslationField('description', $data['description_translations'] ?? []);
        $account->save();

        return redirect()->route('accounts.index')->with('success', __('Account updated successfully.'));
    }

    private function validateAccount(Request $request, ?Account $account = null): array
    {
        $uniqueCodeRule = $account
            ? 'unique:accounts,code,'.$account->id
            : 'unique:accounts,code';

        $rules = [
            'code' => ['required', 'string', 'max:20', $uniqueCodeRule],
            'account_type' => ['required', 'string'],
            'parent_id' => ['nullable', 'exists:accounts,id'],
            'normal_balance' => ['required', 'string'],
            'name_translations' => ['required', 'array'],
            'name_translations.en' => ['required', 'string', 'max:255'],
            'description_translations' => ['nullable', 'array'],
        ];

        foreach (config('locales.supported') as $locale) {
            if ($locale === 'en') {
                continue;
            }

            $rules["name_translations.{$locale}"] = ['nullable', 'string', 'max:255'];
            $rules["description_translations.{$locale}"] = ['nullable', 'string'];
        }

        return $request->validate($rules);
    }
}
