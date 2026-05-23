@extends('layouts.app')

@section('title', $invoice->invoice_number)

@section('content')
<div class="mb-4 d-flex flex-wrap justify-content-between gap-3 align-items-center">
    <a href="{{ route('invoices.index') }}" class="text-decoration-none">&larr; {{ __('Back to invoices') }}</a>
    @can('create-accounting')
        <a href="{{ route('payments.create', ['invoice_id' => $invoice->id]) }}" class="btn btn-primary">{{ __('Record payment') }}</a>
    @endcan
</div>

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">{{ __('Invoice') }} {{ $invoice->invoice_number }}</h1>
        <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $invoice->status)) }}</span>
        <span class="text-muted ms-2">{{ $invoice->invoice_date->format('Y-m-d') }}</span>
        @if($invoice->due_date)
            <span class="text-muted">· {{ __('Due') }} {{ $invoice->due_date->format('Y-m-d') }}</span>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">{{ __('Bill to') }}</div>
            <div class="card-body">
                @if($invoice->client)
                    <p class="mb-1 fw-semibold">{{ $invoice->client->full_name }}</p>
                    @if($invoice->client->email)<p class="mb-1 small">{{ $invoice->client->email }}</p>@endif
                    @if($invoice->client->phone)<p class="mb-1 small">{{ $invoice->client->phone }}</p>@endif
                    @if($invoice->client->address)
                        <p class="small text-muted mb-0">{{ $invoice->client->address }}, {{ $invoice->client->city }} {{ $invoice->client->province }} {{ $invoice->client->postal_code }}</p>
                    @endif
                @else
                    <p class="text-muted mb-0">{{ __('No client on file.') }}</p>
                @endif
            </div>
        </div>

        @if($invoice->property || $invoice->agent)
        <div class="card mb-4">
            <div class="card-body row g-3">
                @if($invoice->property)
                    <div class="col-md-6">
                        <div class="text-muted small">{{ __('Property') }}</div>
                        <div>{{ $invoice->property->full_address }}</div>
                    </div>
                @endif
                @if($invoice->agent)
                    <div class="col-md-6">
                        <div class="text-muted small">{{ __('Agent') }}</div>
                        <div>{{ $invoice->agent->full_name }}</div>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">{{ __('Line items') }}</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Description') }}</th>
                            <th class="text-end">{{ __('Qty') }}</th>
                            <th class="text-end">{{ __('Price') }}</th>
                            <th class="text-end">{{ __('Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->lines as $line)
                        <tr>
                            <td>{{ $line->description }}</td>
                            <td class="text-end">{{ number_format($line->quantity, 2) }}</td>
                            <td class="text-end">${{ number_format($line->unit_price, 2) }}</td>
                            <td class="text-end">${{ number_format($line->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end">{{ __('Subtotal') }}</th>
                            <th class="text-end">${{ number_format($invoice->subtotal, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="3" class="text-end">{{ __('Tax') }}</th>
                            <th class="text-end">${{ number_format($invoice->tax_amount, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="3" class="text-end">{{ __('Total') }}</th>
                            <th class="text-end">${{ number_format($invoice->total, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if($invoice->notes)
        <div class="card">
            <div class="card-header">{{ __('Notes') }}</div>
            <div class="card-body"><p class="mb-0">{{ $invoice->notes }}</p></div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">{{ __('Linked journal entry') }}</div>
            <div class="card-body">
                @if($invoice->journalEntry)
                    <a href="{{ route('journal-entries.show', $invoice->journalEntry) }}">{{ $invoice->journalEntry->reference_number }}</a>
                    <span class="badge bg-secondary ms-1">{{ $invoice->journalEntry->status->label() }}</span>
                @else
                    <span class="text-muted">{{ __('None') }}</span>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">{{ __('Payments') }}</div>
            <div class="list-group list-group-flush">
                @forelse($invoice->payments as $payment)
                    <div class="list-group-item">
                        <div class="small text-muted">{{ $payment->payment_date->format('Y-m-d') }} · {{ $payment->payment_number }}</div>
                        <div>${{ number_format($payment->amount, 2) }}</div>
                        <span class="badge bg-light text-dark">{{ $payment->payment_method?->label() ?? $payment->payment_method }}</span>
                        @if($payment->journalEntry)
                            <div class="mt-1 small"><a href="{{ route('journal-entries.show', $payment->journalEntry) }}">{{ __('Journal') }}</a></div>
                        @endif
                    </div>
                @empty
                    <div class="list-group-item text-muted">{{ __('No payments recorded.') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
