@extends('layouts.app')

@section('title', __('Reports'))

@section('content')
<h1 class="h3 mb-4">{{ __('Reports') }}</h1>

<div class="row g-4">
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h6">{{ __('Trial Balance') }}</h2>
                <p class="small text-muted mb-3">{{ __('Debits and credits by account — as-of date.') }}</p>
                <a href="{{ route('reports.trial-balance') }}" class="btn btn-sm btn-outline-primary">{{ __('Open') }}</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h6">{{ __('Balance Sheet') }}</h2>
                <p class="small text-muted mb-3">{{ __('Assets, liabilities, and equity snapshot.') }}</p>
                <a href="{{ route('reports.balance-sheet') }}" class="btn btn-sm btn-outline-primary">{{ __('Open') }}</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h6">{{ __('Income Statement') }}</h2>
                <p class="small text-muted mb-3">{{ __('Revenue, cost of sales, and expenses.') }}</p>
                <a href="{{ route('reports.income-statement') }}" class="btn btn-sm btn-outline-primary">{{ __('Open') }}</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h6">{{ __('Cash Flow') }}</h2>
                <p class="small text-muted mb-3">{{ __('Cash inflows and outflows.') }}</p>
                <a href="{{ route('reports.cash-flow') }}" class="btn btn-sm btn-outline-primary">{{ __('Open') }}</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h6">{{ __('General Ledger') }}</h2>
                <p class="small text-muted mb-3">{{ __('Activity detail for one account.') }}</p>
                <a href="{{ route('reports.general-ledger') }}" class="btn btn-sm btn-outline-primary">{{ __('Open') }}</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h6">{{ __('Journal Register') }}</h2>
                <p class="small text-muted mb-3">{{ __('Posted journals in a date range.') }}</p>
                <a href="{{ route('reports.journal-register') }}" class="btn btn-sm btn-outline-primary">{{ __('Open') }}</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h6">{{ __('A/R Aging') }}</h2>
                <p class="small text-muted mb-3">{{ __('Open invoices by aging bucket.') }}</p>
                <a href="{{ route('reports.ar-aging') }}" class="btn btn-sm btn-outline-primary">{{ __('Open') }}</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h6">{{ __('Expense Summary') }}</h2>
                <p class="small text-muted mb-3">{{ __('Approved expense totals by category.') }}</p>
                <a href="{{ route('reports.expense-summary') }}" class="btn btn-sm btn-outline-primary">{{ __('Open') }}</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h6">{{ __('Revenue by Dimension') }}</h2>
                <p class="small text-muted mb-3">{{ __('Invoice totals by client, property, and agent.') }}</p>
                <a href="{{ route('reports.revenue-by-dimension') }}" class="btn btn-sm btn-outline-primary">{{ __('Open') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection
