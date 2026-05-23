<?php

namespace Database\Seeders;

use App\Models\TransactionTemplate;
use App\Models\TransactionTemplateLine;
use Illuminate\Database\Seeder;

class TransactionTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Invoice Creation',
                'slug' => 'invoice_creation',
                'description' => 'Record a new invoice with accounts receivable and revenue.',
                'trigger_action_type' => 'invoice_create',
                'variables' => ['amount', 'tax', 'client', 'property'],
                'links_to' => ['invoice'],
                'lines' => [
                    ['1110', 'debit', 'amount', 'Commission receivable - {client}'],
                    ['4010', 'credit', 'amount', 'Brokerage commission income'],
                ],
            ],
            [
                'name' => 'Invoice Payment - Cash',
                'slug' => 'invoice_payment_cash',
                'description' => 'Record cash payment received for an invoice.',
                'trigger_action_type' => 'invoice_payment',
                'variables' => ['amount', 'payment_method', 'client'],
                'links_to' => ['invoice', 'payment'],
                'lines' => [
                    ['1010', 'debit', 'amount', 'Cash received - {client}'],
                    ['1110', 'credit', 'amount', 'Commission receivable cleared'],
                ],
            ],
            [
                'name' => 'Invoice Payment - Bank Transfer',
                'slug' => 'invoice_payment_bank',
                'description' => 'Record bank transfer payment for an invoice.',
                'trigger_action_type' => 'invoice_payment',
                'variables' => ['amount', 'payment_method', 'client'],
                'links_to' => ['invoice', 'payment'],
                'lines' => [
                    ['1020', 'debit', 'amount', 'Bank deposit - {client}'],
                    ['1110', 'credit', 'amount', 'Commission receivable cleared'],
                ],
            ],
            [
                'name' => 'Invoice Payment - Stripe',
                'slug' => 'invoice_payment_stripe',
                'description' => 'Record Stripe payment for an invoice.',
                'trigger_action_type' => 'invoice_payment',
                'variables' => ['amount', 'payment_method', 'client'],
                'links_to' => ['invoice', 'payment'],
                'lines' => [
                    ['1040', 'debit', 'amount', 'Stripe clearing - {client}'],
                    ['1110', 'credit', 'amount', 'Commission receivable cleared'],
                ],
            ],
            [
                'name' => 'Expense Paid - Cash',
                'slug' => 'expense_paid_cash',
                'description' => 'Record expense paid in cash.',
                'trigger_action_type' => 'expense_payment',
                'variables' => ['amount', 'payment_method', 'property'],
                'links_to' => ['expense', 'payment'],
                'lines' => [
                    ['6050', 'debit', 'amount', 'Maintenance expense - {property}'],
                    ['1010', 'credit', 'amount', 'Cash payment'],
                ],
            ],
            [
                'name' => 'Expense Paid - Bank',
                'slug' => 'expense_paid_bank',
                'description' => 'Record expense paid by bank transfer.',
                'trigger_action_type' => 'expense_payment',
                'variables' => ['amount', 'payment_method', 'property'],
                'links_to' => ['expense', 'payment'],
                'lines' => [
                    ['6050', 'debit', 'amount', 'Maintenance expense - {property}'],
                    ['1020', 'credit', 'amount', 'Bank payment'],
                ],
            ],
            [
                'name' => 'Expense Paid - Credit Card',
                'slug' => 'expense_paid_credit_card',
                'description' => 'Record expense paid by credit card.',
                'trigger_action_type' => 'expense_payment',
                'variables' => ['amount', 'payment_method', 'property'],
                'links_to' => ['expense', 'payment'],
                'lines' => [
                    ['6050', 'debit', 'amount', 'Maintenance expense - {property}'],
                    ['2050', 'credit', 'amount', 'Credit card payable'],
                ],
            ],
            [
                'name' => 'Agent Commission Payout',
                'slug' => 'agent_commission_payout',
                'description' => 'Pay agent commission from payable account.',
                'trigger_action_type' => 'commission_payout',
                'variables' => ['amount', 'agent', 'commission_rate'],
                'links_to' => ['payment'],
                'lines' => [
                    ['2020', 'debit', 'amount', 'Agent commission payable - {agent}'],
                    ['1020', 'credit', 'amount', 'Bank payment to agent'],
                ],
            ],
            [
                'name' => 'Brokerage Commission Income',
                'slug' => 'brokerage_commission_income',
                'description' => 'Record brokerage commission income and agent payable.',
                'trigger_action_type' => 'commission_income',
                'variables' => ['amount', 'agent', 'commission_rate', 'property'],
                'links_to' => ['invoice'],
                'lines' => [
                    ['1110', 'debit', 'amount', 'Commission receivable'],
                    ['4010', 'credit', 'amount', 'Brokerage commission income'],
                    ['5010', 'debit', 'agent_amount', 'Agent commission expense'],
                    ['2020', 'credit', 'agent_amount', 'Agent commission payable'],
                ],
            ],
            [
                'name' => 'Rental Income',
                'slug' => 'rental_income',
                'description' => 'Record rental income received.',
                'trigger_action_type' => 'rental_income',
                'variables' => ['amount', 'property', 'client'],
                'links_to' => ['payment'],
                'lines' => [
                    ['1020', 'debit', 'amount', 'Bank deposit - rental'],
                    ['4030', 'credit', 'amount', 'Rental income - {property}'],
                ],
            ],
            [
                'name' => 'Property Maintenance Expense',
                'slug' => 'property_maintenance',
                'description' => 'Record property maintenance expense.',
                'trigger_action_type' => 'maintenance',
                'variables' => ['amount', 'property'],
                'links_to' => ['expense'],
                'lines' => [
                    ['6050', 'debit', 'amount', 'Maintenance - {property}'],
                    ['2010', 'credit', 'amount', 'Accounts payable'],
                ],
            ],
            [
                'name' => 'Insurance Payment',
                'slug' => 'insurance_payment',
                'description' => 'Record insurance premium payment.',
                'trigger_action_type' => 'insurance',
                'variables' => ['amount', 'property'],
                'links_to' => ['expense', 'payment'],
                'lines' => [
                    ['6040', 'debit', 'amount', 'Insurance expense - {property}'],
                    ['1020', 'credit', 'amount', 'Bank payment - insurance'],
                ],
            ],
            [
                'name' => 'Tax Payment',
                'slug' => 'tax_payment',
                'description' => 'Record property tax payment.',
                'trigger_action_type' => 'tax',
                'variables' => ['amount', 'property'],
                'links_to' => ['expense', 'payment'],
                'lines' => [
                    ['6060', 'debit', 'amount', 'Property tax - {property}'],
                    ['1020', 'credit', 'amount', 'Bank payment - taxes'],
                ],
            ],
            [
                'name' => 'Mortgage Payment',
                'slug' => 'mortgage_payment',
                'description' => 'Split mortgage payment between principal and interest.',
                'trigger_action_type' => 'mortgage_payment',
                'variables' => ['principal', 'interest', 'property'],
                'links_to' => ['payment', 'amortization'],
                'lines' => [
                    ['2100', 'debit', 'principal', 'Mortgage principal - {property}'],
                    ['6070', 'debit', 'interest', 'Mortgage interest - {property}'],
                    ['1020', 'credit', 'amount', 'Bank payment - mortgage'],
                ],
            ],
            [
                'name' => 'Amortization Entry',
                'slug' => 'amortization_entry',
                'description' => 'Record scheduled amortization payment.',
                'trigger_action_type' => 'amortization',
                'variables' => ['principal', 'interest', 'property'],
                'links_to' => ['amortization'],
                'lines' => [
                    ['2100', 'debit', 'principal', 'Mortgage principal reduction'],
                    ['6070', 'debit', 'interest', 'Mortgage interest expense'],
                    ['1020', 'credit', 'amount', 'Mortgage payment'],
                ],
            ],
        ];

        foreach ($templates as $templateData) {
            $lines = $templateData['lines'];
            unset($templateData['lines']);

            $template = TransactionTemplate::updateOrCreate(
                ['slug' => $templateData['slug']],
                $templateData
            );

            $template->lines()->delete();

            foreach ($lines as $order => [$accountCode, $side, $amountVar, $desc]) {
                TransactionTemplateLine::create([
                    'template_id' => $template->id,
                    'account_code' => $accountCode,
                    'side' => $side,
                    'amount_variable' => $amountVar,
                    'description_template' => $desc,
                    'line_order' => $order + 1,
                ]);
            }
        }
    }
}
