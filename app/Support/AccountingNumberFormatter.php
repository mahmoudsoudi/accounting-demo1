<?php

namespace App\Support;

class AccountingNumberFormatter
{
    /**
     * Format a signed amount using accounting notation.
     *
     * Positive: $1,234.56
     * Negative: ($1,234.56)
     * Zero:     $0.00
     */
    public static function format(
        float|int|string|null $amount,
        int $decimals = 2,
        bool $includeCurrency = true,
    ): string {
        $value = (float) ($amount ?? 0);
        $formatted = number_format(abs($value), $decimals);

        if ($value < 0) {
            return $includeCurrency
                ? '($'.$formatted.')'
                : '('.$formatted.')';
        }

        return $includeCurrency ? '$'.$formatted : $formatted;
    }

    public static function isNegative(float|int|string|null $amount): bool
    {
        return (float) ($amount ?? 0) < 0;
    }
}
