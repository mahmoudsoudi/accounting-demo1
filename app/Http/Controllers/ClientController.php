<?php

namespace App\Http\Controllers;

use App\Enums\TrustTransactionType;
use App\Models\Client;
use App\Models\TrustTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): View
    {
        $clients = Client::query()
            ->select('clients.*')
            ->selectSub(
                TrustTransaction::query()
                    ->selectRaw(Client::trustBalanceSelectExpression())
                    ->whereColumn('trust_transactions.client_id', 'clients.id'),
                'trust_balance'
            )
            ->orderBy('last_name')
            ->paginate(20);

        return view('clients.index', compact('clients'));
    }

    public function create(): View
    {
        return view('clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Client::create($this->validateClient($request));

        return redirect()->route('clients.index')->with('success', __('Client created.'));
    }

    public function show(Client $client): View
    {
        $client->loadCount(['properties', 'invoices']);

        $runningBalance = 0.0;
        $transactions = $client->trustTransactions()
            ->with(['property'])
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get()
            ->map(function ($transaction) use (&$runningBalance) {
                $delta = match ($transaction->transaction_type) {
                    TrustTransactionType::Deposit => (float) $transaction->amount,
                    TrustTransactionType::Disbursement, TrustTransactionType::Transfer => -(float) $transaction->amount,
                };
                $runningBalance += $delta;

                return [
                    'transaction' => $transaction,
                    'running_balance' => $runningBalance,
                ];
            })
            ->reverse()
            ->values();

        return view('clients.show', [
            'client' => $client,
            'trustBalance' => $runningBalance,
            'transactions' => $transactions,
        ]);
    }

    public function edit(Client $client): View
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $data = $this->validateClient($request);
        $data['is_active'] = $request->boolean('is_active');

        $client->update($data);

        return redirect()->route('clients.show', $client)->with('success', __('Client updated.'));
    }

    private function validateClient(Request $request): array
    {
        return $request->validate([
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'province' => ['nullable', 'string', 'max:2'],
            'postal_code' => ['nullable', 'string'],
        ]);
    }
}
