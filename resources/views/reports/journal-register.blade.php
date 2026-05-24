@extends('layouts.app')

@section('title', __('Journal Register'))

@section('content')
<div class="mb-4">
    <a href="{{ route('reports.index') }}" class="text-decoration-none">&larr; {{ __('Back to reports') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('Journal Register') }}</h1>

<form method="GET" action="{{ route('reports.journal-register') }}" class="row g-3 align-items-end mb-4">
    <div class="col-md-3">
        <label class="form-label" for="start_date">{{ __('Start date') }}</label>
        <input type="text" name="start_date" id="start_date" autocomplete="off" class="form-control datepicker" value="{{ $startDate ?? '' }}" placeholder="{{ __('Optional') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="end_date">{{ __('End date') }}</label>
        <input type="text" name="end_date" id="end_date" autocomplete="off" class="form-control datepicker" value="{{ $endDate ?? '' }}" placeholder="{{ __('Optional') }}">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">{{ __('Apply') }}</button>
    </div>
</form>

@if($entries->isEmpty())
    <div class="alert alert-secondary">{{ __('No journal entries matching filters.') }}</div>
@endif

@foreach($entries as $entry)
<div class="card mb-3">
    <div class="card-header d-flex flex-wrap justify-content-between gap-2 align-items-center">
        <span>
            <a href="{{ route('journal-entries.show', $entry) }}">{{ $entry->reference_number }}</a>
            · {{ $entry->transaction_date->format('Y-m-d') }}
            · <span class="badge bg-light text-dark">{{ $entry->status->label() }}</span>
        </span>
        <span class="small text-muted">{{ $entry->creator?->name }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Account') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th class="text-end">{{ __('Debit') }}</th>
                    <th class="text-end">{{ __('Credit') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entry->lines as $line)
                <tr>
                    <td>{{ $line->account?->full_name ?? '—' }}</td>
                    <td>{{ $line->description ?? '—' }}</td>
                    <td class="text-end">{{ number_format($line->debit, 2) }}</td>
                    <td class="text-end">{{ number_format($line->credit, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endforeach
@endsection
