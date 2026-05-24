@extends('layouts.app')

@section('title', __('Invoices'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h1 class="h3 mb-0">{{ __('Invoices') }}</h1>
    @can('create-accounting')
        <a href="{{ route('invoices.create') }}" class="btn btn-primary">{{ __('New Invoice') }}</a>
    @endcan
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Number') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Client') }}</th>
                    <th>{{ __('Property') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th class="text-end">{{ __('Total') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                    <td>{{ $invoice->client?->full_name ?? '—' }}</td>
                    <td>{{ $invoice->property?->address ?? '—' }}</td>
                    <td><span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $invoice->status)) }}</span></td>
                    <td class="text-end">${{ number_format($invoice->total, 2) }}</td>
                    <td class="text-end">
                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary">{{ __('View') }}</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">{{ __('No invoices.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($invoices->hasPages())
        <div class="card-footer">{{ $invoices->links() }}</div>
    @endif
</div>
@endsection
