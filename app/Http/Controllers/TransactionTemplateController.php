<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\TransactionTemplate;
use App\Models\TransactionTemplateLine;
use App\Services\Accounting\TransactionTemplateService;
use App\Support\TemplateVariable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TransactionTemplateController extends Controller
{
    public function index(): View
    {
        $templates = TransactionTemplate::withCount('lines')->orderBy('name')->get();

        return view('templates.index', compact('templates'));
    }

    public function create(): View
    {
        Gate::authorize('create-accounting');

        return view('templates.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create-accounting');

        $data = $this->validatedTemplateData($request);
        $lines = $data['lines'];
        unset($data['lines']);

        $data['slug'] = $this->uniqueSlug($data['slug'] ?? Str::slug($data['name']));
        $data['variables'] = TemplateVariable::collectFromLines($lines, $data['context_variables'] ?? []);
        unset($data['context_variables']);

        $template = DB::transaction(function () use ($data, $lines) {
            $template = TransactionTemplate::create($data);
            $this->syncLines($template, $lines);

            return $template;
        });

        return redirect()->route('templates.show', $template)->with('success', __('Template created successfully.'));
    }

    public function show(TransactionTemplate $template): View
    {
        $template->load('lines');

        return view('templates.show', compact('template'));
    }

    public function edit(TransactionTemplate $template): View
    {
        Gate::authorize('create-accounting');

        $template->load('lines');

        return view('templates.edit', array_merge($this->formData(), compact('template')));
    }

    public function update(Request $request, TransactionTemplate $template): RedirectResponse
    {
        Gate::authorize('create-accounting');

        $data = $this->validatedTemplateData($request, $template);
        $lines = $data['lines'];
        unset($data['lines']);

        $data['slug'] = $this->uniqueSlug($data['slug'] ?? Str::slug($data['name']), $template->id);
        $data['variables'] = TemplateVariable::collectFromLines($lines, $data['context_variables'] ?? []);
        unset($data['context_variables']);

        DB::transaction(function () use ($template, $data, $lines) {
            $template->update($data);
            $this->syncLines($template, $lines);
        });

        return redirect()->route('templates.show', $template)->with('success', __('Template updated successfully.'));
    }

    public function destroy(TransactionTemplate $template): RedirectResponse
    {
        Gate::authorize('create-accounting');

        $template->delete();

        return redirect()->route('templates.index')->with('success', __('Template deleted successfully.'));
    }

    public function applyForm(TransactionTemplate $template): View
    {
        Gate::authorize('create-accounting');

        $template->load('lines');

        return view('templates.apply', [
            'template' => $template,
            'applicationVariables' => $template->applicationVariables(),
        ]);
    }

    public function apply(Request $request, TransactionTemplate $template, TransactionTemplateService $service): RedirectResponse
    {
        Gate::authorize('create-accounting');

        $template->load('lines');
        $variables = $request->validate($this->applyValidationRules($template));

        $entry = $service->apply($template, $variables, $request->user());

        return redirect()->route('journal-entries.show', $entry)->with('success', __('Template applied successfully.'));
    }

    protected function formData(): array
    {
        return [
            'accounts' => Account::active()->orderBy('code')->get(),
            'amountVariables' => TemplateVariable::amountVariables(),
            'contextVariables' => TemplateVariable::contextVariables(),
            'triggerTypes' => $this->triggerTypes(),
        ];
    }

    protected function triggerTypes(): array
    {
        return [
            'custom' => __('Custom (manual apply only)'),
            'invoice_create' => __('Invoice creation'),
            'invoice_payment' => __('Invoice payment'),
            'expense_payment' => __('Expense payment'),
            'commission_payout' => __('Commission payout'),
            'commission_income' => __('Commission income'),
            'rental_income' => __('Rental income'),
            'maintenance' => __('Maintenance'),
            'insurance' => __('Insurance'),
            'tax' => __('Property tax'),
            'mortgage_payment' => __('Mortgage payment'),
            'amortization' => __('Amortization'),
            'income_tax_accrual' => __('Income tax accrual'),
            'income_tax_payment' => __('Income tax payment'),
            'income_tax_installment' => __('Income tax installment'),
        ];
    }

    protected function validatedTemplateData(Request $request, ?TransactionTemplate $template = null): array
    {
        $slugRule = ['required', 'string', 'max:255', 'alpha_dash'];
        $slugRule[] = $template
            ? 'unique:transaction_templates,slug,'.$template->id
            : 'unique:transaction_templates,slug';

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => $slugRule,
            'description' => ['nullable', 'string'],
            'trigger_action_type' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'context_variables' => ['nullable', 'array'],
            'context_variables.*' => ['string', 'in:'.implode(',', TemplateVariable::contextVariables())],
            'lines' => ['required', 'array', 'min:2'],
            'lines.*.account_code' => ['required', 'string', 'exists:accounts,code'],
            'lines.*.side' => ['required', 'in:debit,credit'],
            'lines.*.amount_variable' => ['required', 'string', 'in:'.implode(',', TemplateVariable::amountVariables())],
            'lines.*.description_template' => ['nullable', 'string', 'max:255'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }

    protected function applyValidationRules(TransactionTemplate $template): array
    {
        $rules = [
            'transaction_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];

        foreach ($template->applicationVariables() as $variable) {
            if (TemplateVariable::isNumeric($variable)) {
                $rules[$variable] = ['nullable', 'numeric', 'min:0'];
            } else {
                $rules[$variable] = ['nullable', 'string', 'max:255'];
            }
        }

        return $rules;
    }

    protected function syncLines(TransactionTemplate $template, array $lines): void
    {
        $template->lines()->delete();

        foreach (array_values($lines) as $order => $line) {
            TransactionTemplateLine::create([
                'template_id' => $template->id,
                'account_code' => $line['account_code'],
                'side' => $line['side'],
                'amount_variable' => $line['amount_variable'],
                'description_template' => $line['description_template'] ?? null,
                'line_order' => $order + 1,
            ]);
        }
    }

    protected function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = trim($slug) !== '' ? trim($slug) : 'template';
        $candidate = $base;
        $suffix = 1;

        while (
            TransactionTemplate::query()
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->where('slug', $candidate)
                ->exists()
        ) {
            $candidate = $base.'-'.$suffix;
            $suffix++;
        }

        return $candidate;
    }
}
