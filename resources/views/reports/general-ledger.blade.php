@extends('layouts.app')

@section('title', __('General Ledger'))

@section('content')
<div class="mb-4">
    <a href="{{ route('reports.index') }}" class="text-decoration-none">&larr; {{ __('Back to reports') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('General Ledger') }}</h1>

<form method="GET" action="{{ route('reports.general-ledger') }}" class="row g-3 align-items-end mb-4">
    <div class="col-md-4">
        <label class="form-label" for="account_ids">{{ __('Accounts') }}</label>
        <div class="mb-1">
            <button type="button" class="btn btn-link btn-sm p-0" id="pick-all-accounts">{{ __('Pick all') }}</button>
            <span class="text-muted mx-1">|</span>
            <button type="button" class="btn btn-link btn-sm p-0" id="clear-all-accounts">{{ __('Clear all') }}</button>
        </div>
        <select name="account_ids[]" id="account_ids" class="form-select" multiple size="10">
            @foreach($accounts as $acc)
                <option value="{{ $acc->id }}" @selected(in_array($acc->id, $accountIds ?? [], true))>{{ $acc->full_name }}</option>
            @endforeach
        </select>
        <div class="form-text">{{ __('Hold Ctrl/Cmd to select multiple, or use Pick all.') }}</div>
    </div>
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

@if(!empty($accountIds) && $ledgers->isNotEmpty())
    @foreach($ledgers as $section)
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
@elseif(!empty($accountIds) && $ledgers->isEmpty())
    <div class="alert alert-info">{{ __('No postings found for selected filters.') }}</div>
@else
    <div class="alert alert-secondary">{{ __('Select an account to view ledger detail.') }}</div>
@endif
@endsection

@push('scripts')
<script>
    $(function () {
        $('#pick-all-accounts').on('click', function () {
            $('#account_ids option').prop('selected', true);
        });

        $('#clear-all-accounts').on('click', function () {
            $('#account_ids option').prop('selected', false);
        });
    });
</script>
@endpush
