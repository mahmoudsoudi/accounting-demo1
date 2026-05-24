@extends('layouts.app')

@section('title', __('Trust Transactions'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h1 class="h3 mb-0">{{ __('Trust Transactions') }}</h1>
    @can('create-accounting')
        <a href="{{ route('trust-transactions.create') }}" class="btn btn-primary">{{ __('New Trust Transaction') }}</a>
    @endcan
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Number') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Client') }}</th>
                    <th>{{ __('Property') }}</th>
                    <th class="text-end">{{ __('Amount') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trustTransactions as $trustTransaction)
                <tr>
                    <td>{{ $trustTransaction->trust_number }}</td>
                    <td><span class="badge bg-secondary">{{ $trustTransaction->transaction_type->label() }}</span></td>
                    <td>{{ $trustTransaction->transaction_date->format('Y-m-d') }}</td>
                    <td>{{ $trustTransaction->client?->full_name ?? '—' }}</td>
                    <td>{{ $trustTransaction->property?->address ?? '—' }}</td>
                    <td class="text-end">${{ number_format($trustTransaction->amount, 2) }}</td>
                    <td class="text-end">
                        <a href="{{ route('trust-transactions.show', $trustTransaction) }}" class="btn btn-sm btn-outline-primary">{{ __('View') }}</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">{{ __('No trust transactions.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($trustTransactions->hasPages())
        <div class="card-footer">{{ $trustTransactions->links() }}</div>
    @endif
</div>
@endsection
