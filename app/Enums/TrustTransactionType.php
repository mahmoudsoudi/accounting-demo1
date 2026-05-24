<?php

namespace App\Enums;

enum TrustTransactionType: string
{
    case Deposit = 'deposit';
    case Disbursement = 'disbursement';
    case Transfer = 'transfer';

    public function label(): string
    {
        return match ($this) {
            self::Deposit => __('Deposit'),
            self::Disbursement => __('Disbursement'),
            self::Transfer => __('Transfer'),
        };
    }
}
