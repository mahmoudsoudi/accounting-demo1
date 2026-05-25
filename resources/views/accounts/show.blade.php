@extends('layouts.app')

@section('title', $account->full_name)

@section('content')
<div class="mb-4 d-flex flex-wrap justify-content-between gap-3 align-items-center">
    <a href="{{ route('accounts.index') }}" class="text-decoration-none">&larr; {{ __('Back to accounts') }}</a>
    <a href="{{ route('accounts.edit', $account) }}" class="btn btn-outline-secondary btn-sm">{{ __('Edit') }}</a>
</div>

<div class="d-flex flex-wrap justify-content-between gap-3 align-items-start mb-4">
    <div>
        <h1 class="h3 mb-2"><code>{{ $account->code }}</code> {{ $account->name }}</h1>
        @if($account->is_active)
            <span class="badge bg-success">{{ __('Active') }}</span>
        @else
            <span class="badge bg-secondary">{{ __('Inactive') }}</span>
        @endif
    </div>
    <div class="text-lg-end">
        <div class="text-muted small">{{ __('Current balance') }}</div>
        <div class="h4 mb-0"><x-accounting-amount :amount="$currentBalance" /></div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">{{ __('Details') }}</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <strong>{{ __('Type') }}</strong><br>
                    {{ $account->account_type->label() }}
                </li>
                <li class="list-group-item">
                    <strong>{{ __('Normal balance') }}</strong><br>
                    {{ $account->normal_balance->label() }}
                </li>
                <li class="list-group-item">
                    <strong>{{ __('Parent account') }}</strong><br>
                    @if($account->parent)
                        <a href="{{ route('accounts.show', $account->parent) }}">{{ $account->parent->full_name }}</a>
                    @else
                        —
                    @endif
                </li>
                @if($account->description)
                    <li class="list-group-item">
                        <strong>{{ __('Description') }}</strong><br>
                        {{ $account->description }}
                    </li>
                @endif
            </ul>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>{{ __('Sub-accounts') }}</span>
                <span class="badge bg-secondary">{{ $account->children->count() }}</span>
            </div>
            @if($account->children->isNotEmpty())
                <ul class="list-group list-group-flush">
                    @foreach($account->children as $child)
                        <li class="list-group-item">
                            <a href="{{ route('accounts.show', $child) }}">{{ $child->full_name }}</a>
                            @if(!$child->is_active)
                                <span class="badge bg-secondary ms-1">{{ __('Inactive') }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="card-body text-muted">{{ __('No sub-accounts.') }}</div>
            @endif
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span>{{ __('Transactions') }}</span>
        @can('view-reports')
            <a href="{{ route('reports.general-ledger', ['account_ids' => [$account->id], 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="small text-decoration-none">
                {{ __('Open in General Ledger') }} &rarr;
            </a>
        @endcan
    </div>

    <div class="card-body border-bottom">
        <form method="GET" action="{{ route('accounts.show', $account) }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label" for="start_date">{{ __('Start date') }}</label>
                <input type="text" name="start_date" id="start_date" autocomplete="off" class="form-control datepicker" value="{{ $startDate }}">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="end_date">{{ __('End date') }}</label>
                <input type="text" name="end_date" id="end_date" autocomplete="off" class="form-control datepicker" value="{{ $endDate }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                @if($startDate || $endDate)
                    <a href="{{ route('accounts.show', $account) }}" class="btn btn-outline-secondary">{{ __('Clear') }}</a>
                @endif
            </div>
        </form>
    </div>

    @if($rows->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Journal entry') }}</th>
                        <th>{{ __('Memo') }}</th>
                        <th class="text-end">{{ __('Debit') }}</th>
                        <th class="text-end">{{ __('Credit') }}</th>
                        <th class="text-end">{{ __('Balance') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                        @php $line = $row['line']; @endphp
                        <tr>
                            <td>{{ $line->journalEntry?->transaction_date?->format('Y-m-d') }}</td>
                            <td>
                                @if($line->journalEntry)
                                    <a href="{{ route('journal-entries.show', $line->journalEntry) }}">{{ $line->journalEntry->reference_number }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $line->description ?? '—' }}</td>
                            <td class="text-end">{{ number_format($line->debit, 2) }}</td>
                            <td class="text-end">{{ number_format($line->credit, 2) }}</td>
                            <td class="text-end"><x-accounting-amount :amount="$row['running_balance']" :currency="false" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="card-body text-muted">{{ __('No transactions found for this account.') }}</div>
    @endif
</div>
@endsection
