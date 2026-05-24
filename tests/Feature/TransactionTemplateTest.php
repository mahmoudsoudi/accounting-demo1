<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\TransactionTemplate;
use App\Models\User;
use App\Services\Accounting\TransactionTemplateService;
use App\Support\TemplateVariable;
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

    public function test_rental_income_template_exposes_only_relevant_apply_fields(): void
    {
        $template = TransactionTemplate::where('slug', 'rental_income')->firstOrFail();
        $template->load('lines');

        $variables = $template->applicationVariables();

        $this->assertContains('amount', $variables);
        $this->assertContains('property', $variables);
        $this->assertContains('client', $variables);
        $this->assertNotContains('principal', $variables);
        $this->assertNotContains('commission_rate', $variables);
    }

    public function test_commission_rate_is_treated_as_percentage_when_agent_amount_is_missing(): void
    {
        $admin = User::where('email', 'admin')->first();
        $service = app(TransactionTemplateService::class);
        $template = TransactionTemplate::where('slug', 'brokerage_commission_income')->firstOrFail();

        $entry = $service->apply($template, [
            'amount' => 1000,
            'commission_rate' => 2.5,
        ], $admin);

        $agentLines = $entry->lines->filter(fn ($line) => $line->debit == 25.0 || $line->credit == 25.0);
        $this->assertCount(2, $agentLines);
        $this->assertTrue($entry->isBalanced());
    }

    public function test_admin_can_create_custom_template(): void
    {
        $admin = User::where('email', 'admin')->first();

        $response = $this->actingAs($admin)->post(route('templates.store'), [
            'name' => 'Office Supplies',
            'slug' => 'office_supplies',
            'description' => 'Buy office supplies with cash.',
            'trigger_action_type' => 'custom',
            'is_active' => 1,
            'context_variables' => ['property'],
            'lines' => [
                [
                    'account_code' => '6050',
                    'side' => 'debit',
                    'amount_variable' => 'amount',
                    'description_template' => 'Supplies - {property}',
                ],
                [
                    'account_code' => '1010',
                    'side' => 'credit',
                    'amount_variable' => 'amount',
                    'description_template' => 'Cash payment',
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $template = TransactionTemplate::where('slug', 'office_supplies')->firstOrFail();
        $response->assertRedirect(route('templates.show', $template));
        $this->assertNotNull($template);
        $this->assertCount(2, $template->lines);
        $this->assertEqualsCanonicalizing(['amount', 'property'], $template->applicationVariables());
    }

    public function test_apply_form_renders_only_template_fields(): void
    {
        $admin = User::where('email', 'admin')->first();
        $template = TransactionTemplate::where('slug', 'mortgage_payment')->firstOrFail();

        $response = $this->actingAs($admin)->get(route('templates.apply', $template));

        $response->assertOk();
        $response->assertSee('name="principal"', false);
        $response->assertSee('name="interest"', false);
        $response->assertDontSee('name="commission_rate"', false);
        $response->assertDontSee('name="client"', false);
    }

    public function test_template_variable_helper_collects_placeholders_from_lines(): void
    {
        $variables = TemplateVariable::collectFromLines([
            [
                'amount_variable' => 'amount',
                'description_template' => 'Payment from {client} for {property}',
            ],
        ], ['agent']);

        $this->assertEqualsCanonicalizing(['agent', 'amount', 'client', 'property'], $variables);
    }
}
