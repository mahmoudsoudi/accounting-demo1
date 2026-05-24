<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    protected $model = \App\Models\Expense::class;

    public function definition(): array
    {
        return [
            'expense_number' => 'EXP-TEST-'.$this->faker->unique()->numberBetween(1, 9999),
            'vendor_name' => $this->faker->company(),
            'expense_date' => now()->toDateString(),
            'status' => 'pending',
            'amount' => 100,
            'tax_amount' => 13,
            'total' => 113,
            'created_by' => User::query()->value('id') ?? User::factory(),
        ];
    }
}
