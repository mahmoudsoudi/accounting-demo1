<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Agent;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\TransactionTemplate;
use App\Services\Accounting\TransactionTemplateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(): View
    {
        $invoices = Invoice::with(['client', 'property'])->latest()->paginate(20);

        return view('invoices.index', compact('invoices'));
    }

    public function create(): View
    {
        Gate::authorize('create-accounting');

        return view('invoices.create', [
            'clients' => Client::where('is_active', true)->orderBy('last_name')->get(),
            'properties' => Property::where('is_active', true)->orderBy('address')->get(),
            'agents' => Agent::where('is_active', true)->orderBy('last_name')->get(),
            'accounts' => Account::active()->orderBy('code')->get(),
        ]);
    }

    public function store(Request $request, TransactionTemplateService $templateService): RedirectResponse
    {
        Gate::authorize('create-accounting');

        $data = $request->validate([
            'client_id' => ['nullable', 'exists:clients,id'],
            'property_id' => ['nullable', 'exists:properties,id'],
            'agent_id' => ['nullable', 'exists:agents,id'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.description' => ['required', 'string'],
            'lines.*.quantity' => ['required', 'numeric', 'min:0'],
            'lines.*.unit_price' => ['required', 'numeric', 'min:0'],
            'lines.*.account_id' => ['nullable', 'exists:accounts,id'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $subtotal = collect($data['lines'])->sum(fn ($l) => $l['quantity'] * $l['unit_price']);
        $tax = (float) ($data['tax_amount'] ?? 0);
        $total = $subtotal + $tax;

        $invoice = Invoice::create([
            'invoice_number' => 'INV-'.now()->format('Ymd').'-'.str_pad((string) (Invoice::count() + 1), 4, '0', STR_PAD_LEFT),
            'client_id' => $data['client_id'] ?? null,
            'property_id' => $data['property_id'] ?? null,
            'agent_id' => $data['agent_id'] ?? null,
            'invoice_date' => $data['invoice_date'],
            'due_date' => $data['due_date'] ?? null,
            'status' => 'sent',
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total' => $total,
            'notes' => $data['notes'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        foreach ($data['lines'] as $line) {
            $invoice->lines()->create([
                'description' => $line['description'],
                'quantity' => $line['quantity'],
                'unit_price' => $line['unit_price'],
                'amount' => $line['quantity'] * $line['unit_price'],
                'account_id' => $line['account_id'] ?? null,
            ]);
        }

        $template = TransactionTemplate::where('slug', 'invoice_creation')->first();
        if ($template) {
            $client = $invoice->client?->full_name ?? 'Client';
            $entry = $templateService->apply($template, [
                'amount' => $total,
                'tax' => $tax,
                'client' => $client,
                'property' => $invoice->property?->address ?? '',
                'transaction_date' => $data['invoice_date'],
            ], $request->user(), 'INV-JE-'.$invoice->invoice_number);
            $invoice->update(['journal_entry_id' => $entry->id]);
        }

        return redirect()->route('invoices.show', $invoice)->with('success', __('Invoice created.'));
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['client', 'property', 'agent', 'lines', 'journalEntry', 'payments']);

        return view('invoices.show', compact('invoice'));
    }
}
