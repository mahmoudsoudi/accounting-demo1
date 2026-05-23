<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use App\Services\Accounting\FinancialStatementService;
use App\Services\Accounting\JournalEntryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialStatementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedAccountingDefaults();
    }

    public function test_financial_statements_use_posted_entries_only(): void
    {
        $admin = User::where('email', 'admin')->first();
        $journalService = app(JournalEntryService::class);
        $financials = app(FinancialStatementService::class);

        $revenue = Account::where('code', '4010')->first();
        $bank = Account::where('code', '1020')->first();

        $draft = $journalService->create([
            'transaction_date' => now()->toDateString(),
            'lines' => [
                ['account_id' => $bank->id, 'debit' => 5000, 'credit' => 0],
                ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 5000],
            ],
        ], $admin);

        $incomeBefore = $financials->incomeStatement();
        $this->assertEquals(0, $incomeBefore['total_revenue']);

        $journalService->post($draft, $admin);

        $incomeAfter = $financials->incomeStatement();
        $this->assertEquals(5000, $incomeAfter['total_revenue']);
    }
}
