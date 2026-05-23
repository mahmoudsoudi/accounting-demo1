@extends('layouts.app')

@section('title', __('Payments'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h1 class="h3 mb-0">{{ __('Payments') }}</h1>
    @can('create-accounting')
        <a href="{{ route('payments.create') }}" class="btn btn-primary">{{ __('Record payment') }}</a>
    @endcan
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Number') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Invoice') }}</th>
                    <th>{{ __('Method') }}</th>
                    <th class="text-end">{{ __('Amount') }}</th>
                    <th>{{ __('Created by') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                @php $inv = ($payment->payable_type === \App\Models\Invoice::class) ? $payment->payable : null; @endphp
                <tr>
                    <td>{{ $payment->payment_number }}</td>
                    <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                    <td>
                        @if($inv instanceof \App\Models\Invoice)
                            <a href="{{ route('invoices.show', $inv) }}">{{ $inv->invoice_number }}</a>
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $payment->payment_method?->label() }}</td>
                    <td class="text-end">${{ number_format($payment->amount, 2) }}</td>
                    <td>{{ $payment->creator?->name }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">{{ __('No payments.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
        <div class="card-footer">{{ $payments->links() }}</div>
    @endif
</div>
@endsection
