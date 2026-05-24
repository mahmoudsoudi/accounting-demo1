<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\Payment;
use App\Services\Accounting\FinancialStatementService;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoDataSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedAccountingDefaults();
    }

    public function test_demo_data_seeder_creates_a_year_of_posted_activity(): void
    {
        $this->seed(DemoDataSeeder::class);

        $this->assertGreaterThanOrEqual(4, Agent::where('license_number', 'like', 'DEMO-%')->count());
        $this->assertGreaterThan(20, Invoice::where('invoice_number', 'like', 'DEMO-INV-%')->count());
        $this->assertGreaterThan(15, Payment::where('payment_number', 'like', 'DEMO-PAY-%')->count());
        $this->assertGreaterThan(15, Expense::where('expense_number', 'like', 'DEMO-EXP-%')->count());
        $this->assertGreaterThan(50, JournalEntry::count());

        $year = (int) now()->subYear()->year;
        $financials = app(FinancialStatementService::class);
        $income = $financials->incomeStatement(
            startDate: "{$year}-01-01",
            endDate: "{$year}-12-31",
        );

        $this->assertGreaterThan(0, $income['total_revenue']);
    }

    public function test_demo_data_seeder_is_idempotent(): void
    {
        $this->seed(DemoDataSeeder::class);
        $countAfterFirstRun = JournalEntry::count();

        $this->seed(DemoDataSeeder::class);

        $this->assertSame($countAfterFirstRun, JournalEntry::count());
    }
}
