<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Services\Accounting\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function generalLedger(Request $request)
    {
        $accountId = $request->input('account_id');
        $from = $request->input('from');
        $to = $request->input('to');

        $accounts = Account::where('is_active', true)->orderBy('code')->get();

        if (!$accountId) {
            return view('accounting.reports.general_ledger', compact('accounts'));
        }

        $data = $this->reportService->generalLedger($accountId, $from, $to);

        return view('accounting.reports.general_ledger',
            array_merge(['accounts' => $accounts], $data)
        );
    }

    public function trialBalance(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $data = $this->reportService->trialBalance($from, $to);

        return view('accounting.reports.trial_balance', $data);
    }

    public function profitLoss(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $data = $this->reportService->profitLoss($from, $to);

        return view('accounting.reports.profit_loss', $data);
    }

    public function balanceSheet(Request $request)
    {
        $to = $request->input('to');

        $data = $this->reportService->balanceSheet($to);

        return view('accounting.reports.balance_sheet', $data);
    }
}
