<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\TransactionTemplate;
use App\Models\User;
use App\Services\Accounting\TransactionTemplateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedAccountingDefaults();
    }

    public function test_chart_of_accounts_seed_data_exists(): void
    {
        $this->assertGreaterThan(30, Account::count());
        $this->assertNotNull(Account::where('code', '4010')->first());
        $this->assertNotNull(Account::where('code', '1110')->first());
    }

    public function test_transaction_templates_create_correct_journal_lines(): void
    {
        $this->assertGreaterThanOrEqual(15, TransactionTemplate::count());

        $template = TransactionTemplate::where('slug', 'invoice_payment_cash')->first();
        $this->assertCount(2, $template->lines);
    }

    public function test_invoice_payment_templates_for_cash_bank_stripe(): void
    {
        foreach (['invoice_payment_cash', 'invoice_payment_bank', 'invoice_payment_stripe'] as $slug) {
            $template = TransactionTemplate::where('slug', $slug)->first();
            $this->assertNotNull($template, "Missing template: {$slug}");
            $this->assertCount(2, $template->lines);
        }
    }

    public function test_expense_templates_for_cash_bank_credit_card(): void
    {
        foreach (['expense_paid_cash', 'expense_paid_bank', 'expense_paid_credit_card'] as $slug) {
            $template = TransactionTemplate::where('slug', $slug)->first();
            $this->assertNotNull($template, "Missing template: {$slug}");
        }
    }

    public function test_template_application_creates_balanced_entry(): void
    {
        $admin = User::where('email', 'admin')->first();
        $service = app(TransactionTemplateService::class);
        $template = TransactionTemplate::where('slug', 'rental_income')->first();

        $entry = $service->apply($template, ['amount' => 1500, 'property' => '123 Main St'], $admin);

        $this->assertTrue($entry->isBalanced());
        $this->assertCount(2, $entry->lines);
    }
}
