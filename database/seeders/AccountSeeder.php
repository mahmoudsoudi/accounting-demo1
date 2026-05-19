<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asset accounts
        $assets = Account::create([
            'code' => '1000',
            'name' => 'Assets',
            'type' => 'asset',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);

        Account::create([
            'parent_id' => $assets->id,
            'code' => '1010',
            'name' => 'Cash',
            'type' => 'asset',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);

        Account::create([
            'parent_id' => $assets->id,
            'code' => '1020',
            'name' => 'Bank',
            'type' => 'asset',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);

        Account::create([
            'parent_id' => $assets->id,
            'code' => '1100',
            'name' => 'Accounts Receivable',
            'type' => 'asset',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);

        Account::create([
            'parent_id' => $assets->id,
            'code' => '1200',
            'name' => 'Inventory',
            'type' => 'asset',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);

        // Liability accounts
        $liabilities = Account::create([
            'code' => '2000',
            'name' => 'Liabilities',
            'type' => 'liability',
            'normal_balance' => 'credit',
            'is_active' => true,
        ]);

        Account::create([
            'parent_id' => $liabilities->id,
            'code' => '2010',
            'name' => 'Accounts Payable',
            'type' => 'liability',
            'normal_balance' => 'credit',
            'is_active' => true,
        ]);

        Account::create([
            'parent_id' => $liabilities->id,
            'code' => '2100',
            'name' => 'VAT Payable',
            'type' => 'liability',
            'normal_balance' => 'credit',
            'is_active' => true,
        ]);

        // Equity accounts
        $equity = Account::create([
            'code' => '3000',
            'name' => 'Equity',
            'type' => 'equity',
            'normal_balance' => 'credit',
            'is_active' => true,
        ]);

        Account::create([
            'parent_id' => $equity->id,
            'code' => '3010',
            'name' => 'Owner Capital',
            'type' => 'equity',
            'normal_balance' => 'credit',
            'is_active' => true,
        ]);

        Account::create([
            'parent_id' => $equity->id,
            'code' => '3020',
            'name' => 'Retained Earnings',
            'type' => 'equity',
            'normal_balance' => 'credit',
            'is_active' => true,
        ]);

        // Revenue accounts
        $revenue = Account::create([
            'code' => '4000',
            'name' => 'Revenue',
            'type' => 'revenue',
            'normal_balance' => 'credit',
            'is_active' => true,
        ]);

        Account::create([
            'parent_id' => $revenue->id,
            'code' => '4010',
            'name' => 'Sales Revenue',
            'type' => 'revenue',
            'normal_balance' => 'credit',
            'is_active' => true,
        ]);

        Account::create([
            'parent_id' => $revenue->id,
            'code' => '4020',
            'name' => 'Service Revenue',
            'type' => 'revenue',
            'normal_balance' => 'credit',
            'is_active' => true,
        ]);

        // Expense accounts
        $expenses = Account::create([
            'code' => '5000',
            'name' => 'Expenses',
            'type' => 'expense',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);

        Account::create([
            'parent_id' => $expenses->id,
            'code' => '5010',
            'name' => 'Cost of Goods Sold',
            'type' => 'expense',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);

        Account::create([
            'parent_id' => $expenses->id,
            'code' => '6010',
            'name' => 'Rent Expense',
            'type' => 'expense',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);

        Account::create([
            'parent_id' => $expenses->id,
            'code' => '6020',
            'name' => 'Salaries Expense',
            'type' => 'expense',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);

        Account::create([
            'parent_id' => $expenses->id,
            'code' => '6030',
            'name' => 'Software Expense',
            'type' => 'expense',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);

        Account::create([
            'parent_id' => $expenses->id,
            'code' => '6040',
            'name' => 'Marketing Expense',
            'type' => 'expense',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);

        Account::create([
            'parent_id' => $expenses->id,
            'code' => '6050',
            'name' => 'Bank Fees',
            'type' => 'expense',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);
    }
}
