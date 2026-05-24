@extends('layouts.app')

@section('title', __('Trust Fund Report'))

@section('content')
<div class="mb-4">
    <a href="{{ route('reports.index') }}" class="text-decoration-none">&larr; {{ __('Back to reports') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('Trust Fund Report') }}</h1>

<form method="GET" action="{{ route('reports.trust-fund') }}" class="row g-3 align-items-end mb-4">
    <div class="col-md-3">
        <label class="form-label" for="start_date">{{ __('Start date') }}</label>
        <input type="text" name="start_date" id="start_date" autocomplete="off" class="form-control datepicker" value="{{ $startDate }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="end_date">{{ __('End date') }}</label>
        <input type="text" name="end_date" id="end_date" autocomplete="off" class="form-control datepicker" value="{{ $endDate }}">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">{{ __('Run report') }}</button>
    </div>
</form>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">{{ __('Trust asset balance') }}</div>
                <div class="h4 mb-0">${{ number_format($data['asset_balance'], 2) }}</div>
                @if($data['trust_asset'])
                    <div class="small text-muted">{{ $data['trust_asset']->full_name }}</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">{{ __('Trust liability balance') }}</div>
                <div class="h4 mb-0">${{ number_format($data['liability_balance'], 2) }}</div>
                @if($data['trust_liability'])
                    <div class="small text-muted">{{ $data['trust_liability']->full_name }}</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">{{ __('Shortfall / excess') }}</div>
                <div class="h4 mb-0 @if(abs($data['shortfall_excess']) > 0.01) text-danger @else text-success @endif">
                    ${{ number_format($data['shortfall_excess'], 2) }}
                </div>
                <div class="small text-muted">{{ __('Asset minus liability') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">{{ __('Period activity') }}</div>
                <div class="small">{{ __('Deposits') }}: <strong>${{ number_format($periodDeposits, 2) }}</strong></div>
                <div class="small">{{ __('Disbursements') }}: <strong>${{ number_format($periodDisbursements, 2) }}</strong></div>
            </div>
        </div>
    </div>
</div>

@if($data['ledgers']->isNotEmpty())
    @foreach($data['ledgers'] as $section)
        <div class="card mb-4">
            <div class="card-header fw-semibold">{{ $section['account']->full_name }}</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Reference') }}</th>
                            <th>{{ __('Memo') }}</th>
                            <th class="text-end">{{ __('Debit') }}</th>
                            <th class="text-end">{{ __('Credit') }}</th>
                            <th class="text-end">{{ __('Balance') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($section['rows'] as $r)
                            @php $line = $r['line']; @endphp
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
                                <td class="text-end">{{ number_format($r['running_balance'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@else
    <div class="alert alert-info mb-4">{{ __('No trust account postings found for selected period.') }}</div>
@endif

<div class="card">
    <div class="card-header fw-semibold">{{ __('Trust transactions') }}</div>
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Number') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Client') }}</th>
                    <th>{{ __('Property') }}</th>
                    <th class="text-end">{{ __('Amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                    <tr>
                        <td>
                            <a href="{{ route('trust-transactions.show', $transaction) }}">{{ $transaction->trust_number }}</a>
                        </td>
                        <td>{{ $transaction->transaction_type->label() }}</td>
                        <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                        <td>{{ $transaction->client?->full_name ?? '—' }}</td>
                        <td>{{ $transaction->property?->address ?? '—' }}</td>
                        <td class="text-end">${{ number_format($transaction->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">{{ __('No trust transactions in this period.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
