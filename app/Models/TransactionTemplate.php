<?php

namespace App\Models;

use App\Support\TemplateVariable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionTemplate extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'trigger_action_type',
        'variables',
        'links_to',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'links_to' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function lines(): HasMany
    {
        return $this->hasMany(TransactionTemplateLine::class, 'template_id')->orderBy('line_order');
    }

    public function applicationVariables(): array
    {
        $variables = collect($this->variables ?? [])
            ->filter(fn ($variable) => is_string($variable));

        foreach ($this->lines as $line) {
            if ($line->amount_variable) {
                $variables->push($line->amount_variable);
            }

            foreach (TemplateVariable::placeholdersFromText($line->description_template) as $placeholder) {
                $variables->push($placeholder);
            }
        }

        if ($variables->contains(TemplateVariable::COMMISSION_RATE) && ! $variables->contains(TemplateVariable::AGENT_AMOUNT)) {
            $variables->push(TemplateVariable::AGENT_AMOUNT);
        }

        return $variables
            ->unique()
            ->sort()
            ->values()
            ->all();
    }
}
