<?php

namespace App\Http\Controllers;

use App\Models\TransactionTemplate;
use App\Services\Accounting\TransactionTemplateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TransactionTemplateController extends Controller
{
    public function index(): View
    {
        $templates = TransactionTemplate::withCount('lines')->orderBy('name')->get();

        return view('templates.index', compact('templates'));
    }

    public function show(TransactionTemplate $template): View
    {
        $template->load('lines');

        return view('templates.show', compact('template'));
    }

    public function applyForm(TransactionTemplate $template): View
    {
        Gate::authorize('create-accounting');

        return view('templates.apply', compact('template'));
    }

    public function apply(Request $request, TransactionTemplate $template, TransactionTemplateService $service): RedirectResponse
    {
        Gate::authorize('create-accounting');

        $variables = $request->validate([
            'amount' => ['nullable', 'numeric', 'min:0'],
            'principal' => ['nullable', 'numeric', 'min:0'],
            'interest' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'commission_rate' => ['nullable', 'numeric', 'min:0'],
            'agent_amount' => ['nullable', 'numeric', 'min:0'],
            'client' => ['nullable', 'string'],
            'agent' => ['nullable', 'string'],
            'property' => ['nullable', 'string'],
            'transaction_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $entry = $service->apply($template, $variables, $request->user());

        return redirect()->route('journal-entries.show', $entry)->with('success', __('Template applied successfully.'));
    }
}
