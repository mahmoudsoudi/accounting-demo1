<?php

namespace Tests\Unit;

use App\Support\AccountingNumberFormatter;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AccountingNumberFormatterTest extends TestCase
{
    #[DataProvider('amountProvider')]
    public function test_it_formats_amounts_using_accounting_notation(
        float|int|string|null $amount,
        string $expected,
        bool $includeCurrency = true,
    ): void {
        $this->assertSame($expected, AccountingNumberFormatter::format($amount, 2, $includeCurrency));
    }

    public static function amountProvider(): array
    {
        return [
            'positive' => [733.95, '$733.95'],
            'negative' => [-733.95, '($733.95)'],
            'zero' => [0, '$0.00'],
            'null' => [null, '$0.00'],
            'large negative' => [-1234.5, '($1,234.50)'],
            'negative without currency' => [-733.95, '(733.95)', false],
        ];
    }

    public function test_it_detects_negative_amounts(): void
    {
        $this->assertTrue(AccountingNumberFormatter::isNegative(-1));
        $this->assertFalse(AccountingNumberFormatter::isNegative(0));
        $this->assertFalse(AccountingNumberFormatter::isNegative(100));
    }
}
