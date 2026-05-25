@extends('layouts.app')

@section('title', $client->full_name)

@section('content')
<div class="mb-4 d-flex flex-wrap justify-content-between gap-3 align-items-center">
    <a href="{{ route('clients.index') }}" class="text-decoration-none">&larr; {{ __('Back to clients') }}</a>
    <a href="{{ route('clients.edit', $client) }}" class="btn btn-outline-secondary btn-sm">{{ __('Edit') }}</a>
</div>

<div class="d-flex flex-wrap justify-content-between gap-3 align-items-start mb-4">
    <div>
        <h1 class="h3 mb-2">{{ $client->full_name }}</h1>
        @if($client->is_active)
            <span class="badge bg-success">{{ __('Active') }}</span>
        @else
            <span class="badge bg-secondary">{{ __('Inactive') }}</span>
        @endif
    </div>
    <div class="text-lg-end">
        <div class="text-muted small">{{ __('Trust balance') }}</div>
        <div class="h4 mb-0"><x-accounting-amount :amount="$trustBalance" /></div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">{{ __('Details') }}</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>{{ __('Email') }}</strong><br>{{ $client->email ?? '—' }}</li>
                <li class="list-group-item"><strong>{{ __('Phone') }}</strong><br>{{ $client->phone ?? '—' }}</li>
                <li class="list-group-item"><strong>{{ __('Address') }}</strong><br>{{ $client->address ?? '—' }}</li>
                <li class="list-group-item">
                    <strong>{{ __('City') }}</strong><br>
                    @if($client->city || $client->province || $client->postal_code)
                        {{ trim(collect([$client->city, $client->province, $client->postal_code])->filter()->join(', ')) }}
                    @else
                        —
                    @endif
                </li>
            </ul>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">{{ __('Summary') }}</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>{{ __('Properties') }}</span>
                    <span class="badge bg-secondary">{{ $client->properties_count }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>{{ __('Invoices') }}</span>
                    <span class="badge bg-secondary">{{ $client->invoices_count }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>{{ __('Trust transactions') }}</span>
                    <span class="badge bg-secondary">{{ $transactions->count() }}</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span>{{ __('Trust transactions') }}</span>
        @can('create-accounting')
            <a href="{{ route('trust-transactions.create') }}" class="small text-decoration-none">
                {{ __('New Trust Transaction') }} &rarr;
            </a>
        @endcan
    </div>

    @if($transactions->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Number') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Property') }}</th>
                        <th class="text-end">{{ __('Amount') }}</th>
                        <th class="text-end">{{ __('Balance') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $row)
                        @php $transaction = $row['transaction']; @endphp
                        <tr>
                            <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                            <td>{{ $transaction->trust_number }}</td>
                            <td><span class="badge bg-secondary">{{ $transaction->transaction_type->label() }}</span></td>
                            <td>{{ $transaction->property?->address ?? '—' }}</td>
                            <td class="text-end">${{ number_format($transaction->amount, 2) }}</td>
                            <td class="text-end"><x-accounting-amount :amount="$row['running_balance']" :currency="false" /></td>
                            <td class="text-end">
                                <a href="{{ route('trust-transactions.show', $transaction) }}" class="btn btn-sm btn-outline-primary">{{ __('View') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="card-body text-muted">{{ __('No trust transactions.') }}</div>
    @endif
</div>
@endsection
