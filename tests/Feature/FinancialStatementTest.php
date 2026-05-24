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

    public function test_income_statement_calculates_ebitda_and_income_tax(): void
    {
        $admin = User::where('email', 'admin')->first();
        $journalService = app(JournalEntryService::class);
        $financials = app(FinancialStatementService::class);

        $bank = Account::where('code', '1020')->first();
        $revenue = Account::where('code', '4010')->first();
        $rent = Account::where('code', '6030')->first();
        $interest = Account::where('code', '6070')->first();
        $depreciation = Account::where('code', '6110')->first();
        $incomeTax = Account::where('code', '6120')->first();
        $incomeTaxPayable = Account::where('code', '2060')->first();

        $entries = [
            [
                ['account_id' => $bank->id, 'debit' => 100000, 'credit' => 0],
                ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 100000],
            ],
            [
                ['account_id' => $rent->id, 'debit' => 20000, 'credit' => 0],
                ['account_id' => $bank->id, 'debit' => 0, 'credit' => 20000],
            ],
            [
                ['account_id' => $depreciation->id, 'debit' => 5000, 'credit' => 0],
                ['account_id' => $bank->id, 'debit' => 0, 'credit' => 5000],
            ],
            [
                ['account_id' => $interest->id, 'debit' => 3000, 'credit' => 0],
                ['account_id' => $bank->id, 'debit' => 0, 'credit' => 3000],
            ],
            [
                ['account_id' => $incomeTax->id, 'debit' => 15000, 'credit' => 0],
                ['account_id' => $incomeTaxPayable->id, 'debit' => 0, 'credit' => 15000],
            ],
        ];

        foreach ($entries as $lines) {
            $entry = $journalService->create([
                'transaction_date' => now()->toDateString(),
                'lines' => $lines,
            ], $admin);
            $journalService->post($entry, $admin);
        }

        $income = $financials->incomeStatement();

        $this->assertEquals(100000, $income['total_revenue']);
        $this->assertEquals(20000, $income['total_expenses']);
        $this->assertEquals(80000, $income['ebitda']);
        $this->assertEquals(75000, $income['ebit']);
        $this->assertEquals(72000, $income['ebt']);
        $this->assertEquals(15000, $income['total_income_tax_expense']);
        $this->assertEquals(57000, $income['net_income_after_tax']);
        $this->assertEquals(57000, $income['net_income']);
    }
}
