<?php

namespace App\Enums;

enum JournalEntryStatus: string
{
    case Draft = 'draft';
    case Posted = 'posted';
    case Approved = 'approved';
    case Reversed = 'reversed';

    public function label(): string
    {
        return match ($this) {
            self::Draft => __('Draft'),
            self::Posted => __('Posted'),
            self::Approved => __('Approved'),
            self::Reversed => __('Reversed'),
        };
    }
}
