<?php

namespace App\Services\Calculators;

class CommissionCalculatorService
{
    public function calculate(
        float $salePrice,
        float $commissionRate,
        float $agentSplitRate,
        float $referralFee = 0
    ): array {
        $totalCommission = $salePrice * ($commissionRate / 100);
        $afterReferral = $totalCommission - $referralFee;
        $agentAmount = $afterReferral * ($agentSplitRate / 100);
        $brokerageAmount = $afterReferral - $agentAmount;

        return [
            'sale_price' => round($salePrice, 2),
            'commission_rate' => $commissionRate,
            'total_commission' => round($totalCommission, 2),
            'referral_fee' => round($referralFee, 2),
            'agent_amount' => round($agentAmount, 2),
            'brokerage_amount' => round($brokerageAmount, 2),
            'agent_split_rate' => $agentSplitRate,
        ];
    }
}
