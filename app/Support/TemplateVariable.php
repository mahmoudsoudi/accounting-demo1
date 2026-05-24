<?php

namespace App\Support;

class TemplateVariable
{
    public const AMOUNT = 'amount';

    public const PRINCIPAL = 'principal';

    public const INTEREST = 'interest';

    public const TAX = 'tax';

    public const AGENT_AMOUNT = 'agent_amount';

    public const COMMISSION_RATE = 'commission_rate';

    public const CLIENT = 'client';

    public const AGENT = 'agent';

    public const PROPERTY = 'property';

    public const FISCAL_YEAR = 'fiscal_year';

    public static function amountVariables(): array
    {
        return [
            self::AMOUNT,
            self::PRINCIPAL,
            self::INTEREST,
            self::TAX,
            self::AGENT_AMOUNT,
        ];
    }

    public static function contextVariables(): array
    {
        return [
            self::CLIENT,
            self::AGENT,
            self::PROPERTY,
            self::FISCAL_YEAR,
        ];
    }

    public static function all(): array
    {
        return array_merge(
            self::amountVariables(),
            [self::COMMISSION_RATE],
            self::contextVariables(),
        );
    }

    public static function label(string $variable): string
    {
        return match ($variable) {
            self::AMOUNT => __('Amount'),
            self::PRINCIPAL => __('Principal'),
            self::INTEREST => __('Interest'),
            self::TAX => __('Tax'),
            self::AGENT_AMOUNT => __('Agent amount'),
            self::COMMISSION_RATE => __('Commission rate (%)'),
            self::CLIENT => __('Client'),
            self::AGENT => __('Agent'),
            self::PROPERTY => __('Property'),
            self::FISCAL_YEAR => __('Fiscal year'),
            default => ucfirst(str_replace('_', ' ', $variable)),
        };
    }

    public static function isNumeric(string $variable): bool
    {
        return in_array($variable, [...self::amountVariables(), self::COMMISSION_RATE], true);
    }

    public static function placeholdersFromText(?string $text): array
    {
        if ($text === null || $text === '') {
            return [];
        }

        preg_match_all('/\{(\w+)\}/', $text, $matches);

        return $matches[1] ?? [];
    }

    public static function collectFromLines(array $lines, array $selectedContext = []): array
    {
        $variables = collect($selectedContext);

        foreach ($lines as $line) {
            if (! empty($line['amount_variable'])) {
                $variables->push($line['amount_variable']);
            }

            foreach (self::placeholdersFromText($line['description_template'] ?? null) as $placeholder) {
                $variables->push($placeholder);
            }
        }

        return $variables
            ->filter(fn ($variable) => is_string($variable) && $variable !== '')
            ->unique()
            ->sort()
            ->values()
            ->all();
    }
}
