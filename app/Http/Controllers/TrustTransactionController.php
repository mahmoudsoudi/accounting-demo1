<?php

namespace App\Http\Controllers;

use App\Enums\TrustTransactionType;
use App\Models\Client;
use App\Models\Property;
use App\Models\TransactionTemplate;
use App\Models\TrustTransaction;
use App\Services\Accounting\TransactionTemplateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TrustTransactionController extends Controller
{
    public function index(): View
    {
        $trustTransactions = TrustTransaction::with(['client', 'property', 'creator'])
            ->latest()
            ->paginate(20);

        return view('trust-transactions.index', compact('trustTransactions'));
    }

    public function create(): View
    {
        Gate::authorize('create-accounting');

        return view('trust-transactions.create', [
            'clients' => Client::where('is_active', true)->orderBy('last_name')->get(),
            'properties' => Property::where('is_active', true)->orderBy('address')->get(),
            'transactionTypes' => TrustTransactionType::cases(),
            'paymentMethods' => \App\Enums\PaymentMethod::cases(),
        ]);
    }

    public function store(Request $request, TransactionTemplateService $templateService): RedirectResponse
    {
        Gate::authorize('create-accounting');

        $data = $request->validate([
            'transaction_type' => ['required', 'string'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'property_id' => ['nullable', 'exists:properties,id'],
            'transaction_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['nullable', 'string'],
            'reference' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $trustTransaction = TrustTransaction::create([
            'trust_number' => 'TRU-'.now()->format('Ymd').'-'.str_pad((string) (TrustTransaction::count() + 1), 4, '0', STR_PAD_LEFT),
            'transaction_type' => $data['transaction_type'],
            'client_id' => $data['client_id'] ?? null,
            'property_id' => $data['property_id'] ?? null,
            'transaction_date' => $data['transaction_date'],
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'] ?? null,
            'reference' => $data['reference'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        $slug = match ($data['transaction_type']) {
            TrustTransactionType::Deposit->value => 'trust_deposit',
            TrustTransactionType::Disbursement->value => 'trust_disbursement',
            TrustTransactionType::Transfer->value => 'trust_transfer_to_operating',
            default => null,
        };

        if ($slug) {
            $template = TransactionTemplate::where('slug', $slug)->first();
            if ($template) {
                $client = $trustTransaction->client?->full_name ?? 'Client';
                $property = $trustTransaction->property?->address ?? '';

                $entry = $templateService->apply($template, [
                    'amount' => $data['amount'],
                    'client' => $client,
                    'property' => $property,
                    'transaction_date' => $data['transaction_date'],
                ], $request->user(), 'TRU-JE-'.$trustTransaction->trust_number, $trustTransaction);
                $trustTransaction->update(['journal_entry_id' => $entry->id]);
            }
        }

        return redirect()->route('trust-transactions.show', $trustTransaction)->with('success', __('Trust transaction recorded.'));
    }

    public function show(TrustTransaction $trustTransaction): View
    {
        $trustTransaction->load(['client', 'property', 'journalEntry', 'creator']);

        return view('trust-transactions.show', compact('trustTransaction'));
    }
}
