<?php

namespace App\Services\Accounting;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\TransactionTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class TransactionTemplateService
{
    public function __construct(
        protected JournalEntryService $journalEntryService
    ) {}

    public function apply(
        TransactionTemplate $template,
        array $variables,
        User $user,
        ?string $reference = null,
        ?Model $referenceable = null
    ): JournalEntry {
        $template->load('lines');
        $accounts = Account::whereIn('code', $template->lines->pluck('account_code'))->get()->keyBy('code');

        $resolvedAmounts = $this->resolveAmounts($variables);
        $lines = [];

        foreach ($template->lines as $templateLine) {
            $account = $accounts->get($templateLine->account_code);

            if (! $account) {
                throw new InvalidArgumentException(__('Account :code not found for template.', ['code' => $templateLine->account_code]));
            }

            $amount = $this->resolveVariableAmount($templateLine->amount_variable, $resolvedAmounts);
            $description = $this->interpolate($templateLine->description_template ?? '', $variables);

            $lines[] = [
                'account_id' => $account->id,
                'description' => $description,
                'debit' => $templateLine->side === 'debit' ? $amount : 0,
                'credit' => $templateLine->side === 'credit' ? $amount : 0,
            ];
        }

        $entry = $this->journalEntryService->create([
            'reference_number' => $reference,
            'transaction_date' => $variables['transaction_date'] ?? now()->toDateString(),
            'notes' => $variables['notes'] ?? $template->description,
            'lines' => $lines,
            'referenceable' => $referenceable,
        ], $user);

        return $this->journalEntryService->post($entry, $user);
    }

    protected function resolveAmounts(array $variables): array
    {
        $amount = (float) ($variables['amount'] ?? 0);
        $principal = (float) ($variables['principal'] ?? 0);
        $interest = (float) ($variables['interest'] ?? 0);
        $tax = (float) ($variables['tax'] ?? 0);
        $commissionRate = (float) ($variables['commission_rate'] ?? 0);
        $agentAmount = isset($variables['agent_amount'])
            ? (float) $variables['agent_amount']
            : ($amount * ($commissionRate / 100));

        if ($principal + $interest > 0 && $amount === 0.0) {
            $amount = $principal + $interest;
        }

        return [
            'amount' => $amount,
            'principal' => $principal,
            'interest' => $interest,
            'tax' => $tax,
            'agent_amount' => $agentAmount,
        ];
    }

    protected function resolveVariableAmount(string $variable, array $amounts): float
    {
        return (float) ($amounts[$variable] ?? 0);
    }

    protected function interpolate(string $template, array $variables): string
    {
        return preg_replace_callback('/\{(\w+)\}/', function ($matches) use ($variables) {
            return (string) ($variables[$matches[1]] ?? $matches[0]);
        }, $template);
    }
}
