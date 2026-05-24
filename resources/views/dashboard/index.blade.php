@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <h1 class="h3 mb-0">{{ __('Accounting Dashboard') }}</h1>
    <form method="GET" action="{{ route('dashboard') }}" class="d-flex align-items-end gap-2">
        <div>
            <label class="form-label mb-1" for="year">{{ __('Year') }}</label>
            <select name="year" id="year" class="form-select">
                @foreach($availableYears as $year)
                    <option value="{{ $year }}" @selected($year === $selectedYear)>{{ $year }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">{{ __('Apply') }}</button>
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card metric-card success">
            <div class="card-body">
                <div class="text-muted small">{{ __('Total Revenue') }} ({{ $selectedYear }})</div>
                <div class="h4 mb-0">${{ number_format($totalRevenue, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card metric-card">
            <div class="card-body">
                <div class="text-muted small">{{ __('Net Income') }} ({{ $selectedYear }})</div>
                <div class="h4 mb-0">${{ number_format($netIncome, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card metric-card warning">
            <div class="card-body">
                <div class="text-muted small">{{ __('Total Assets') }} ({{ __('as of') }} {{ $endDate }})</div>
                <div class="h4 mb-0">${{ number_format($totalAssets, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card metric-card danger">
            <div class="card-body">
                <div class="text-muted small">{{ __('Net Cash Flow') }} ({{ $selectedYear }})</div>
                <div class="h4 mb-0">${{ number_format($netCashFlow, 2) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">{{ __('Recent Journal Entries') }}</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>{{ __('Reference') }}</th><th>{{ __('Date') }}</th><th>{{ __('Status') }}</th><th>{{ __('Created By') }}</th></tr></thead>
                    <tbody>
                        @forelse($recentJournals as $entry)
                        <tr>
                            <td><a href="{{ route('journal-entries.show', $entry) }}">{{ $entry->reference_number }}</a></td>
                            <td>{{ $entry->transaction_date->format('Y-m-d') }}</td>
                            <td><span class="badge bg-secondary">{{ $entry->status->label() }}</span></td>
                            <td>{{ $entry->creator?->name }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted">{{ __('No journal entries yet.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">{{ __('Quick Stats') }}</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between"><span>{{ __('Pending Expenses') }}</span><strong>{{ $pendingExpenses }}</strong></li>
                <li class="list-group-item d-flex justify-content-between"><span>{{ __('Open Invoices') }}</span><strong>{{ $openInvoices }}</strong></li>
                <li class="list-group-item d-flex justify-content-between"><span>{{ __('Draft Journals') }}</span><strong>{{ $draftJournals }}</strong></li>
                <li class="list-group-item d-flex justify-content-between"><span>{{ __('Active Accounts') }}</span><strong>{{ $accountCount }}</strong></li>
                <li class="list-group-item d-flex justify-content-between"><span>{{ __('Clients') }}</span><strong>{{ $clientCount }}</strong></li>
                <li class="list-group-item d-flex justify-content-between"><span>{{ __('Agents') }}</span><strong>{{ $agentCount }}</strong></li>
                <li class="list-group-item d-flex justify-content-between"><span>{{ __('Properties') }}</span><strong>{{ $propertyCount }}</strong></li>
            </ul>
        </div>
    </div>
</div>
@endsection
