<?php

namespace App\Enums;

enum AccountType: string
{
    case Asset = 'asset';
    case Liability = 'liability';
    case Equity = 'equity';
    case Revenue = 'revenue';
    case CostOfSales = 'cost_of_sales';
    case Expense = 'expense';

    public function label(): string
    {
        return match ($this) {
            self::Asset => __('Asset'),
            self::Liability => __('Liability'),
            self::Equity => __('Equity'),
            self::Revenue => __('Revenue'),
            self::CostOfSales => __('Cost of Sales'),
            self::Expense => __('Operating Expense'),
        };
    }
}
