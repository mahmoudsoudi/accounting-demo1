<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Services\Accounting\JournalEntryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class JournalEntryController extends Controller
{
    public function __construct(
        protected JournalEntryService $journalService
    ) {}

    public function index(): View
    {
        $entries = JournalEntry::with(['creator', 'lines'])->latest()->paginate(20);

        return view('journal-entries.index', compact('entries'));
    }

    public function create(): View
    {
        Gate::authorize('create-accounting');

        return view('journal-entries.create', [
            'accounts' => Account::active()->orderBy('code')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create-accounting');

        $data = $request->validate([
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:2'],
            'lines.*.account_id' => ['required', 'exists:accounts,id'],
            'lines.*.description' => ['nullable', 'string'],
            'lines.*.debit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.credit' => ['nullable', 'numeric', 'min:0'],
        ]);

        $entry = $this->journalService->create($data, $request->user());

        return redirect()->route('journal-entries.show', $entry)->with('success', __('Journal entry created.'));
    }

    public function show(JournalEntry $journalEntry): View
    {
        $journalEntry->load(['lines.account', 'creator', 'approver', 'reverser', 'originalEntry', 'auditLogs.user']);

        return view('journal-entries.show', ['entry' => $journalEntry]);
    }

    public function edit(JournalEntry $journalEntry): View
    {
        Gate::authorize('create-accounting');
        abort_unless($journalEntry->status->value === 'draft', 403);

        return view('journal-entries.edit', [
            'entry' => $journalEntry->load('lines'),
            'accounts' => Account::active()->orderBy('code')->get(),
        ]);
    }

    public function update(Request $request, JournalEntry $journalEntry): RedirectResponse
    {
        Gate::authorize('create-accounting');

        $data = $request->validate([
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:2'],
            'lines.*.account_id' => ['required', 'exists:accounts,id'],
            'lines.*.description' => ['nullable', 'string'],
            'lines.*.debit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.credit' => ['nullable', 'numeric', 'min:0'],
        ]);

        $this->journalService->update($journalEntry, $data, $request->user());

        return redirect()->route('journal-entries.show', $journalEntry)->with('success', __('Journal entry updated.'));
    }

    public function post(JournalEntry $journalEntry): RedirectResponse
    {
        Gate::authorize('create-accounting');

        try {
            $this->journalService->post($journalEntry, request()->user());
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['balance' => $e->getMessage()]);
        }

        return back()->with('success', __('Journal entry posted.'));
    }

    public function approve(JournalEntry $journalEntry): RedirectResponse
    {
        Gate::authorize('approve-journal-entry', $journalEntry);

        try {
            $this->journalService->approve($journalEntry, request()->user());
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['approve' => $e->getMessage()]);
        }

        return back()->with('success', __('Journal entry approved.'));
    }

    public function reverse(JournalEntry $journalEntry): RedirectResponse
    {
        Gate::authorize('reverse-transactions');

        try {
            $reversal = $this->journalService->reverse($journalEntry, request()->user());
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['reverse' => $e->getMessage()]);
        }

        return redirect()->route('journal-entries.show', $reversal)->with('success', __('Journal entry reversed.'));
    }
}
