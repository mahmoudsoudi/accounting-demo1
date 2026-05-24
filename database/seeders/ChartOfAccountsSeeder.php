<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Enums\NormalBalance;
use App\Models\Account;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // Assets
            ['1000', 'Assets', AccountType::Asset, null, NormalBalance::Debit, 'Top-level asset group'],
            ['1010', 'Cash on Hand', AccountType::Asset, '1000', NormalBalance::Debit, 'Petty cash and cash drawers'],
            ['1020', 'Operating Bank Account', AccountType::Asset, '1000', NormalBalance::Debit, 'Primary operating chequing account'],
            ['1030', 'Trust / Client Funds', AccountType::Asset, '1000', NormalBalance::Debit, 'Segregated client trust funds'],
            ['1040', 'Stripe Clearing', AccountType::Asset, '1000', NormalBalance::Debit, 'Stripe payment clearing account'],
            ['1100', 'Accounts Receivable', AccountType::Asset, '1000', NormalBalance::Debit, 'Trade receivables'],
            ['1110', 'Commission Receivable', AccountType::Asset, '1100', NormalBalance::Debit, 'Commissions due from deals'],
            ['1200', 'Prepaid Expenses', AccountType::Asset, '1000', NormalBalance::Debit, 'Prepaid insurance, taxes, etc.'],
            ['1210', 'Income Tax Prepaid', AccountType::Asset, '1000', NormalBalance::Debit, 'Income tax installments paid in advance'],
            ['1300', 'Property & Equipment', AccountType::Asset, '1000', NormalBalance::Debit, 'Office and property assets'],

            // Liabilities
            ['2000', 'Liabilities', AccountType::Liability, null, NormalBalance::Credit, 'Top-level liability group'],
            ['2010', 'Accounts Payable', AccountType::Liability, '2000', NormalBalance::Credit, 'Trade payables'],
            ['2020', 'Agent Commission Payable', AccountType::Liability, '2000', NormalBalance::Credit, 'Commissions owed to agents'],
            ['2030', 'Trust Funds Payable', AccountType::Liability, '2000', NormalBalance::Credit, 'Client trust obligations'],
            ['2040', 'GST/HST Payable', AccountType::Liability, '2000', NormalBalance::Credit, 'Sales tax collected'],
            ['2050', 'Credit Card Payable', AccountType::Liability, '2000', NormalBalance::Credit, 'Corporate credit card balance'],
            ['2060', 'Income Tax Payable', AccountType::Liability, '2000', NormalBalance::Credit, 'Corporate income tax owed to CRA'],
            ['2100', 'Mortgage Payable', AccountType::Liability, '2000', NormalBalance::Credit, 'Mortgage principal owed'],
            ['2110', 'Mortgage Interest Payable', AccountType::Liability, '2000', NormalBalance::Credit, 'Accrued mortgage interest'],

            // Equity
            ['3000', 'Equity', AccountType::Equity, null, NormalBalance::Credit, 'Owner equity'],
            ['3010', 'Retained Earnings', AccountType::Equity, '3000', NormalBalance::Credit, 'Accumulated retained earnings'],
            ['3020', 'Owner Drawings', AccountType::Equity, '3000', NormalBalance::Debit, 'Owner withdrawals'],

            // Revenue
            ['4000', 'Revenue', AccountType::Revenue, null, NormalBalance::Credit, 'Top-level revenue group'],
            ['4010', 'Brokerage Commission Income', AccountType::Revenue, '4000', NormalBalance::Credit, 'Commission income from transactions'],
            ['4020', 'Property Management Income', AccountType::Revenue, '4000', NormalBalance::Credit, 'Property management fees'],
            ['4030', 'Rental Income', AccountType::Revenue, '4000', NormalBalance::Credit, 'Rental property income'],
            ['4040', 'Listing Fee Income', AccountType::Revenue, '4000', NormalBalance::Credit, 'Listing and marketing fees'],
            ['4050', 'Other Income', AccountType::Revenue, '4000', NormalBalance::Credit, 'Miscellaneous income'],

            // Cost of Sales
            ['5000', 'Cost of Sales', AccountType::CostOfSales, null, NormalBalance::Debit, 'Direct costs of brokerage services'],
            ['5010', 'Agent Commission Expense', AccountType::CostOfSales, '5000', NormalBalance::Debit, 'Agent commission payouts'],
            ['5020', 'Referral Fees', AccountType::CostOfSales, '5000', NormalBalance::Debit, 'Referral commission costs'],

            // Operating Expenses
            ['6000', 'Operating Expenses', AccountType::Expense, null, NormalBalance::Debit, 'Top-level expense group'],
            ['6010', 'Listing Expenses', AccountType::Expense, '6000', NormalBalance::Debit, 'Listing-related costs'],
            ['6020', 'Marketing Expenses', AccountType::Expense, '6000', NormalBalance::Debit, 'Advertising and marketing'],
            ['6030', 'Office Rent', AccountType::Expense, '6000', NormalBalance::Debit, 'Office lease costs'],
            ['6040', 'Insurance Expense', AccountType::Expense, '6000', NormalBalance::Debit, 'Property and liability insurance'],
            ['6050', 'Maintenance Expense', AccountType::Expense, '6000', NormalBalance::Debit, 'Property maintenance and repairs'],
            ['6060', 'Property Tax Expense', AccountType::Expense, '6000', NormalBalance::Debit, 'Municipal property taxes'],
            ['6070', 'Mortgage Interest Expense', AccountType::Expense, '6000', NormalBalance::Debit, 'Mortgage interest paid'],
            ['6080', 'Professional Fees', AccountType::Expense, '6000', NormalBalance::Debit, 'Legal and accounting fees'],
            ['6090', 'Bank Charges', AccountType::Expense, '6000', NormalBalance::Debit, 'Bank service fees'],
            ['6100', 'Stripe Processing Fees', AccountType::Expense, '6000', NormalBalance::Debit, 'Payment processing fees'],
            ['6110', 'Depreciation Expense', AccountType::Expense, '6000', NormalBalance::Debit, 'Depreciation of property and equipment'],
            ['6115', 'Amortization Expense', AccountType::Expense, '6000', NormalBalance::Debit, 'Amortization of intangible assets'],
            ['6120', 'Income Tax Expense', AccountType::Expense, '6000', NormalBalance::Debit, 'Corporate income tax for the period'],
        ];

        $codeToId = [];

        foreach ($accounts as [$code, $name, $type, $parentCode, $balance, $description]) {
            $account = Account::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'account_type' => $type,
                    'parent_id' => $parentCode ? ($codeToId[$parentCode] ?? null) : null,
                    'normal_balance' => $balance,
                    'is_active' => true,
                    'description' => $description,
                ]
            );
            $codeToId[$code] = $account->id;
        }

        // Second pass to fix parent references for child accounts
        foreach ($accounts as [$code, $name, $type, $parentCode, $balance, $description]) {
            if ($parentCode) {
                Account::where('code', $code)->update([
                    'parent_id' => $codeToId[$parentCode] ?? null,
                ]);
            }
        }
    }
}
