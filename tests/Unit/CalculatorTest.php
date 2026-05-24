<?php

namespace Tests\Unit;

use App\Services\Calculators\CommissionCalculatorService;
use App\Services\Calculators\MortgageCalculatorService;
use App\Services\Calculators\RentalCalculatorService;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    public function test_mortgage_calculator(): void
    {
        $service = new MortgageCalculatorService();
        $result = $service->calculate(300000, 5.0, 360);

        $this->assertGreaterThan(0, $result['monthly_payment']);
        $this->assertCount(360, $result['schedule']);
    }

    public function test_rental_calculator(): void
    {
        $service = new RentalCalculatorService();
        $result = $service->calculate(2500);

        $this->assertEquals(30000, $result['gross_annual_income']);
        $this->assertArrayHasKey('net_operating_income', $result);
    }

    public function test_commission_calculator(): void
    {
        $service = new CommissionCalculatorService();
        $result = $service->calculate(500000, 5.0, 60.0);

        $this->assertEquals(25000, $result['total_commission']);
        $this->assertEquals(15000, $result['agent_amount']);
        $this->assertEquals(10000, $result['brokerage_amount']);
    }
}
