<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Agent;
use App\Models\Expense;
use App\Models\Property;
use App\Models\TransactionTemplate;
use App\Services\Accounting\TransactionTemplateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(): View
    {
        $expenses = Expense::with(['property', 'creator'])->latest()->paginate(20);

        return view('expenses.index', compact('expenses'));
    }

    public function create(): View
    {
        Gate::authorize('create-accounting');

        return view('expenses.create', [
            'properties' => Property::where('is_active', true)->orderBy('address')->get(),
            'agents' => Agent::where('is_active', true)->orderBy('last_name')->get(),
            'accounts' => Account::active()->orderBy('code')->get(),
            'paymentMethods' => \App\Enums\PaymentMethod::cases(),
        ]);
    }

    public function store(Request $request, TransactionTemplateService $templateService): RedirectResponse
    {
        Gate::authorize('create-accounting');

        $data = $request->validate([
            'vendor_name' => ['required', 'string', 'max:255'],
            'property_id' => ['nullable', 'exists:properties,id'],
            'agent_id' => ['nullable', 'exists:agents,id'],
            'expense_date' => ['required', 'date'],
            'category' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string'],
            'account_id' => ['nullable', 'exists:accounts,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $tax = (float) ($data['tax_amount'] ?? 0);
        $total = $data['amount'] + $tax;

        $expense = Expense::create([
            'expense_number' => 'EXP-'.now()->format('Ymd').'-'.str_pad((string) (Expense::count() + 1), 4, '0', STR_PAD_LEFT),
            'vendor_name' => $data['vendor_name'],
            'property_id' => $data['property_id'] ?? null,
            'agent_id' => $data['agent_id'] ?? null,
            'expense_date' => $data['expense_date'],
            'category' => $data['category'] ?? null,
            'status' => 'pending',
            'amount' => $data['amount'],
            'tax_amount' => $tax,
            'total' => $total,
            'payment_method' => $data['payment_method'] ?? null,
            'account_id' => $data['account_id'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        $slug = match ($data['payment_method'] ?? '') {
            'cash' => 'expense_paid_cash',
            'bank' => 'expense_paid_bank',
            'credit_card' => 'expense_paid_credit_card',
            default => null,
        };

        if ($slug) {
            $template = TransactionTemplate::where('slug', $slug)->first();
            if ($template) {
                $entry = $templateService->apply($template, [
                    'amount' => $total,
                    'property' => $expense->property?->address ?? '',
                    'transaction_date' => $data['expense_date'],
                ], $request->user(), 'EXP-JE-'.$expense->expense_number);
                $expense->update(['journal_entry_id' => $entry->id]);
            }
        }

        return redirect()->route('expenses.show', $expense)->with('success', __('Expense recorded.'));
    }

    public function show(Expense $expense): View
    {
        $expense->load(['property', 'agent', 'account', 'journalEntry', 'creator', 'approver']);

        return view('expenses.show', compact('expense'));
    }

    public function approve(Expense $expense): RedirectResponse
    {
        Gate::authorize('approve-expense', $expense);

        $expense->update([
            'status' => 'approved',
            'approved_by' => request()->user()->id,
            'approved_at' => now(),
        ]);

        return back()->with('success', __('Expense approved.'));
    }
}
