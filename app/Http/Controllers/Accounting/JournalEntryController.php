<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Accounting\StoreJournalEntryRequest;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Services\Accounting\JournalEntryService;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    public function __construct(
        protected JournalEntryService $journalEntryService
    ) {}

    public function index()
    {
        $entries = JournalEntry::orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);
        return view('accounting.journal_entries.index', compact('entries'));
    }

    public function create()
    {
        $accounts = Account::where('is_active', true)->orderBy('code')->get();
        return view('accounting.journal_entries.create', compact('accounts'));
    }

    public function store(StoreJournalEntryRequest $request)
    {
        $post = $request->input('action') === 'post';

        try {
            $entry = $this->journalEntryService->create(
                $request->validated(),
                $post
            );

            $message = $post
                ? 'Journal entry posted successfully.'
                : 'Journal entry saved as draft.';

            return redirect()->route('accounting.journal-entries.show', $entry)
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(JournalEntry $entry)
    {
        $entry->load('lines.account');
        return view('accounting.journal_entries.show', compact('entry'));
    }

    public function post(JournalEntry $entry)
    {
        try {
            $this->journalEntryService->post($entry);
            return redirect()->route('accounting.journal-entries.show', $entry)
                ->with('success', 'Journal entry posted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reverse(JournalEntry $entry)
    {
        try {
            $reversal = $this->journalEntryService->reverse($entry);
            return redirect()->route('accounting.journal-entries.show', $reversal)
                ->with('success', 'Journal entry reversed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
