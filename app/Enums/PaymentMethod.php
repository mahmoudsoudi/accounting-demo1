<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Bank = 'bank';
    case CreditCard = 'credit_card';
    case Stripe = 'stripe';
    case Cheque = 'cheque';
    case ETransfer = 'e_transfer';

    public function label(): string
    {
        return match ($this) {
            self::Cash => __('Cash'),
            self::Bank => __('Bank Transfer'),
            self::CreditCard => __('Credit Card'),
            self::Stripe => __('Stripe'),
            self::Cheque => __('Cheque'),
            self::ETransfer => __('E-Transfer'),
        };
    }
}
