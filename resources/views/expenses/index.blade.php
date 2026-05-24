@extends('layouts.app')

@section('title', __('Expenses'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h1 class="h3 mb-0">{{ __('Expenses') }}</h1>
    @can('create-accounting')
        <a href="{{ route('expenses.create') }}" class="btn btn-primary">{{ __('New Expense') }}</a>
    @endcan
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Number') }}</th>
                    <th>{{ __('Vendor') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Property') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th class="text-end">{{ __('Total') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                <tr>
                    <td>{{ $expense->expense_number }}</td>
                    <td>{{ $expense->vendor_name }}</td>
                    <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                    <td>{{ $expense->property?->address ?? '—' }}</td>
                    <td><span class="badge bg-secondary">{{ ucfirst($expense->status) }}</span></td>
                    <td class="text-end">${{ number_format($expense->total, 2) }}</td>
                    <td class="text-end">
                        <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-outline-primary">{{ __('View') }}</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">{{ __('No expenses.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($expenses->hasPages())
        <div class="card-footer">{{ $expenses->links() }}</div>
    @endif
</div>
@endsection
