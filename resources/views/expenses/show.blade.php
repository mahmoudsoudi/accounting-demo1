@extends('layouts.app')

@section('title', $expense->expense_number)

@section('content')
<div class="mb-4">
    <a href="{{ route('expenses.index') }}" class="text-decoration-none">&larr; {{ __('Back to expenses') }}</a>
</div>

<div class="d-flex flex-wrap justify-content-between gap-3 align-items-start mb-4">
    <div>
        <h1 class="h3 mb-2">{{ __('Expense') }} {{ $expense->expense_number }}</h1>
        <span class="badge bg-secondary">{{ ucfirst($expense->status) }}</span>
        <span class="text-muted ms-2">{{ $expense->expense_date->format('Y-m-d') }}</span>
    </div>
    @can('approve-expense', $expense)
        @if($expense->status === 'pending')
            <form action="{{ route('expenses.approve', $expense) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success">{{ __('Approve expense') }}</button>
            </form>
        @endif
    @endcan
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">{{ __('Details') }}</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>{{ __('Vendor') }}</strong><br>{{ $expense->vendor_name }}</li>
                <li class="list-group-item"><strong>{{ __('Category') }}</strong><br>{{ $expense->category ?? '—' }}</li>
                <li class="list-group-item"><strong>{{ __('Property') }}</strong><br>{{ $expense->property?->full_address ?? '—' }}</li>
                <li class="list-group-item"><strong>{{ __('Agent') }}</strong><br>{{ $expense->agent?->full_name ?? '—' }}</li>
                <li class="list-group-item"><strong>{{ __('Payment method') }}</strong><br>
                    {{ $expense->payment_method?->label() ?? '—' }}
                </li>
                <li class="list-group-item"><strong>{{ __('Account') }}</strong><br>{{ $expense->account?->full_name ?? '—' }}</li>
            </ul>
        </div>

        @if($expense->notes)
            <div class="card">
                <div class="card-header">{{ __('Notes') }}</div>
                <div class="card-body"><p class="mb-0">{{ $expense->notes }}</p></div>
            </div>
        @endif
    </div>

    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">{{ __('Totals') }}</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between"><span>{{ __('Amount') }}</span><strong>${{ number_format($expense->amount, 2) }}</strong></li>
                <li class="list-group-item d-flex justify-content-between"><span>{{ __('Tax') }}</span><strong>${{ number_format($expense->tax_amount, 2) }}</strong></li>
                <li class="list-group-item d-flex justify-content-between"><span>{{ __('Total') }}</span><strong>${{ number_format($expense->total, 2) }}</strong></li>
            </ul>
        </div>

        <div class="card mb-4">
            <div class="card-header">{{ __('Workflow') }}</div>
            <ul class="list-group list-group-flush small">
                <li class="list-group-item">{{ __('Created by') }}: {{ $expense->creator?->name }}</li>
                @if($expense->approver)
                    <li class="list-group-item">{{ __('Approved by') }}: {{ $expense->approver->name }}</li>
                    <li class="list-group-item">{{ __('Approved at') }}: {{ $expense->approved_at?->format('Y-m-d H:i') }}</li>
                @endif
            </ul>
        </div>

        <div class="card">
            <div class="card-header">{{ __('Journal entry') }}</div>
            <div class="card-body">
                @if($expense->journalEntry)
                    <a href="{{ route('journal-entries.show', $expense->journalEntry) }}">{{ $expense->journalEntry->reference_number }}</a>
                    <span class="badge bg-secondary ms-1">{{ $expense->journalEntry->status->label() }}</span>
                @else
                    <span class="text-muted">{{ __('None') }}</span>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
