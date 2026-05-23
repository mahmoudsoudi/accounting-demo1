<?php

namespace App\Services\Calculators;

class RentalCalculatorService
{
    public function calculate(
        float $monthlyRent,
        float $vacancyRate = 5.0,
        float $managementFeeRate = 8.0,
        float $maintenanceRate = 5.0,
        float $insurance = 0,
        float $taxes = 0,
        float $mortgage = 0,
        float $otherExpenses = 0
    ): array {
        $grossAnnual = $monthlyRent * 12;
        $vacancyLoss = $grossAnnual * ($vacancyRate / 100);
        $effectiveGross = $grossAnnual - $vacancyLoss;
        $managementFee = $effectiveGross * ($managementFeeRate / 100);
        $maintenance = $effectiveGross * ($maintenanceRate / 100);
        $totalExpenses = $managementFee + $maintenance + $insurance + $taxes + $mortgage + $otherExpenses;
        $netOperatingIncome = $effectiveGross - $totalExpenses;

        return [
            'gross_annual_income' => round($grossAnnual, 2),
            'vacancy_loss' => round($vacancyLoss, 2),
            'effective_gross_income' => round($effectiveGross, 2),
            'management_fee' => round($managementFee, 2),
            'maintenance' => round($maintenance, 2),
            'insurance' => round($insurance, 2),
            'taxes' => round($taxes, 2),
            'mortgage' => round($mortgage, 2),
            'other_expenses' => round($otherExpenses, 2),
            'total_expenses' => round($totalExpenses, 2),
            'net_operating_income' => round($netOperatingIncome, 2),
            'monthly_cash_flow' => round($netOperatingIncome / 12, 2),
        ];
    }
}
