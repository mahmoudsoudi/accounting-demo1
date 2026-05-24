<?php

use App\Support\AccountingNumberFormatter;

if (! function_exists('accounting_format')) {
    function accounting_format(
        float|int|string|null $amount,
        int $decimals = 2,
        bool $includeCurrency = true,
    ): string {
        return AccountingNumberFormatter::format($amount, $decimals, $includeCurrency);
    }
}
