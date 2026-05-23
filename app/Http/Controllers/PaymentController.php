<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\TransactionTemplate;
use App\Services\Accounting\TransactionTemplateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        $payments = Payment::with('creator')->latest()->paginate(20);

        return view('payments.index', compact('payments'));
    }

    public function create(Request $request): View
    {
        Gate::authorize('create-accounting');

        return view('payments.create', [
            'invoices' => Invoice::whereIn('status', ['sent', 'partial', 'overdue'])->orderByDesc('invoice_date')->get(),
            'paymentMethods' => \App\Enums\PaymentMethod::cases(),
            'invoiceId' => $request->query('invoice_id'),
        ]);
    }

    public function store(Request $request, TransactionTemplateService $templateService): RedirectResponse
    {
        Gate::authorize('create-accounting');

        $data = $request->validate([
            'invoice_id' => ['required', 'exists:invoices,id'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string'],
            'reference' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $invoice = Invoice::findOrFail($data['invoice_id']);

        $payment = Payment::create([
            'payment_number' => 'PAY-'.now()->format('Ymd').'-'.str_pad((string) (Payment::count() + 1), 4, '0', STR_PAD_LEFT),
            'payable_type' => Invoice::class,
            'payable_id' => $invoice->id,
            'payment_date' => $data['payment_date'],
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'],
            'reference' => $data['reference'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        $slug = match ($data['payment_method']) {
            'cash' => 'invoice_payment_cash',
            'bank' => 'invoice_payment_bank',
            'stripe' => 'invoice_payment_stripe',
            default => 'invoice_payment_bank',
        };

        $template = TransactionTemplate::where('slug', $slug)->first();
        if ($template) {
            $entry = $templateService->apply($template, [
                'amount' => $data['amount'],
                'client' => $invoice->client?->full_name ?? 'Client',
                'transaction_date' => $data['payment_date'],
            ], $request->user(), 'PAY-JE-'.$payment->payment_number);
            $payment->update(['journal_entry_id' => $entry->id]);
        }

        return redirect()->route('payments.index')->with('success', __('Payment recorded.'));
    }
}
