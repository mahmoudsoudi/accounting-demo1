<?php

namespace Tests\Feature;

use App\Enums\TrustTransactionType;
use App\Models\Account;
use App\Models\Client;
use App\Models\TrustTransaction;
use App\Models\User;
use App\Services\Accounting\FinancialStatementService;
use App\Services\Accounting\JournalEntryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrustTransactionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedAccountingDefaults();
    }

    public function test_trust_deposit_creates_balanced_journal_entry(): void
    {
        $admin = User::where('email', 'admin')->first();
        $client = Client::create([
            'first_name' => 'Jane',
            'last_name' => 'Buyer',
            'email' => 'jane@example.com',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('trust-transactions.store'), [
            'transaction_type' => TrustTransactionType::Deposit->value,
            'client_id' => $client->id,
            'transaction_date' => now()->toDateString(),
            'amount' => 5000,
        ]);

        $trustTransaction = TrustTransaction::first();
        $response->assertRedirect(route('trust-transactions.show', $trustTransaction));

        $this->assertNotNull($trustTransaction->journal_entry_id);
        $this->assertDatabaseHas('journal_lines', [
            'journal_entry_id' => $trustTransaction->journal_entry_id,
            'account_id' => Account::where('code', '1030')->first()->id,
            'debit' => 5000,
        ]);
        $this->assertDatabaseHas('journal_lines', [
            'journal_entry_id' => $trustTransaction->journal_entry_id,
            'account_id' => Account::where('code', '2030')->first()->id,
            'credit' => 5000,
        ]);
    }

    public function test_trust_fund_report_shows_balanced_shortfall_excess(): void
    {
        $admin = User::where('email', 'admin')->first();
        $journalService = app(JournalEntryService::class);
        $financials = app(FinancialStatementService::class);

        $trustAsset = Account::where('code', '1030')->first();
        $trustLiability = Account::where('code', '2030')->first();

        $entry = $journalService->create([
            'transaction_date' => now()->toDateString(),
            'lines' => [
                ['account_id' => $trustAsset->id, 'debit' => 8000, 'credit' => 0],
                ['account_id' => $trustLiability->id, 'debit' => 0, 'credit' => 8000],
            ],
        ], $admin);
        $journalService->post($entry, $admin);

        $report = $financials->trustFundReport();

        $this->assertEquals(8000, $report['asset_balance']);
        $this->assertEquals(8000, $report['liability_balance']);
        $this->assertEquals(0, $report['shortfall_excess']);
    }

    public function test_trust_fund_report_page_is_accessible(): void
    {
        $admin = User::where('email', 'admin')->first();

        $this->actingAs($admin)
            ->get(route('reports.trust-fund'))
            ->assertOk()
            ->assertSee(__('Trust Fund Report'));
    }
}
