<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Services\Accounting\ReportService;

class DashboardController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index()
    {
        $totalAccounts = Account::count();
        $postedEntries = JournalEntry::where('status', 'posted')->count();
        $draftEntries = JournalEntry::where('status', 'draft')->count();

        $trialBalance = $this->reportService->trialBalance();
        $isBalanced = $trialBalance['is_balanced'];

        return view('dashboard', compact(
            'totalAccounts',
            'postedEntries',
            'draftEntries',
            'isBalanced'
        ));
    }
}
