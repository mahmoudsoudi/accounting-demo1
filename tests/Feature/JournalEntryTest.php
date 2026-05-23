<?php

namespace Tests\Feature;

use App\Enums\JournalEntryStatus;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\User;
use App\Services\Accounting\JournalEntryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalEntryTest extends TestCase
{
    use RefreshDatabase;

    protected JournalEntryService $service;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedAccountingDefaults();
        $this->service = app(JournalEntryService::class);
        $this->admin = User::where('email', 'admin')->first();
    }

    public function test_journal_entry_must_balance_before_posting(): void
    {
        $accounts = Account::active()->take(2)->get();

        $entry = $this->service->create([
            'transaction_date' => now()->toDateString(),
            'lines' => [
                ['account_id' => $accounts[0]->id, 'debit' => 100, 'credit' => 0],
                ['account_id' => $accounts[1]->id, 'debit' => 0, 'credit' => 50],
            ],
        ], $this->admin);

        $this->expectException(\InvalidArgumentException::class);
        $this->service->post($entry, $this->admin);
    }

    public function test_journal_entry_supports_multiple_line_items(): void
    {
        $accounts = Account::active()->take(3)->get();

        $entry = $this->service->create([
            'transaction_date' => now()->toDateString(),
            'lines' => [
                ['account_id' => $accounts[0]->id, 'debit' => 100, 'credit' => 0],
                ['account_id' => $accounts[1]->id, 'debit' => 50, 'credit' => 0],
                ['account_id' => $accounts[2]->id, 'debit' => 0, 'credit' => 150],
            ],
        ], $this->admin);

        $this->assertCount(3, $entry->lines);
        $this->assertTrue($entry->isBalanced());
    }

    public function test_reversed_journal_preserves_audit_history(): void
    {
        $accounts = Account::active()->take(2)->get();

        $entry = $this->service->create([
            'transaction_date' => now()->toDateString(),
            'lines' => [
                ['account_id' => $accounts[0]->id, 'debit' => 200, 'credit' => 0],
                ['account_id' => $accounts[1]->id, 'debit' => 0, 'credit' => 200],
            ],
        ], $this->admin);

        $this->service->approve($entry, $this->admin);
        $reversal = $this->service->reverse($entry->fresh(), $this->admin);

        $entry->refresh();
        $this->assertEquals(JournalEntryStatus::Reversed, $entry->status);
        $this->assertNotNull($entry->reversed_at);
        $this->assertEquals($entry->id, $reversal->reversal_of_id);
        $this->assertDatabaseHas('audit_logs', ['auditable_id' => $entry->id, 'action' => 'reversed']);
        $this->assertDatabaseHas('journal_entries', ['id' => $entry->id, 'status' => 'reversed']);
    }

    public function test_manager_can_reverse_transactions(): void
    {
        $managerRole = \App\Models\Role::where('slug', 'manager')->first();
        $manager = User::create([
            'name' => 'Manager',
            'email' => 'manager@test.com',
            'password' => bcrypt('password'),
            'role_id' => $managerRole->id,
            'is_active' => true,
        ]);

        $accounts = Account::active()->take(2)->get();
        $entry = $this->service->create([
            'transaction_date' => now()->toDateString(),
            'lines' => [
                ['account_id' => $accounts[0]->id, 'debit' => 100, 'credit' => 0],
                ['account_id' => $accounts[1]->id, 'debit' => 0, 'credit' => 100],
            ],
        ], $this->admin);
        $this->service->approve($entry, $this->admin);

        $reversal = $this->service->reverse($entry->fresh(), $manager);
        $this->assertNotNull($reversal);
    }
}
