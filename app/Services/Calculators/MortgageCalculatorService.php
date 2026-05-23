<?php

namespace App\Services\Calculators;

class MortgageCalculatorService
{
    public function calculate(float $principal, float $annualRate, int $termMonths): array
    {
        $monthlyRate = $annualRate / 100 / 12;

        if ($monthlyRate === 0.0) {
            $payment = $principal / $termMonths;
        } else {
            $payment = $principal * ($monthlyRate * pow(1 + $monthlyRate, $termMonths))
                / (pow(1 + $monthlyRate, $termMonths) - 1);
        }

        $schedule = [];
        $balance = $principal;

        for ($i = 1; $i <= $termMonths; $i++) {
            $interest = $balance * $monthlyRate;
            $principalPaid = $payment - $interest;
            $balance = max(0, $balance - $principalPaid);

            $schedule[] = [
                'payment_number' => $i,
                'payment_amount' => round($payment, 2),
                'principal_amount' => round($principalPaid, 2),
                'interest_amount' => round($interest, 2),
                'remaining_balance' => round($balance, 2),
            ];
        }

        return [
            'monthly_payment' => round($payment, 2),
            'total_interest' => round(collect($schedule)->sum('interest_amount'), 2),
            'total_paid' => round(collect($schedule)->sum('payment_amount'), 2),
            'schedule' => $schedule,
        ];
    }
}
