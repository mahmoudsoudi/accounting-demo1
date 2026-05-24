@extends('layouts.app')

@section('title', __('Cash Flow'))

@section('content')
<div class="mb-4">
    <a href="{{ route('reports.index') }}" class="text-decoration-none">&larr; {{ __('Back to reports') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('Cash Flow Summary') }}</h1>

<form method="GET" action="{{ route('reports.cash-flow') }}" class="row g-3 align-items-end mb-4">
    <div class="col-md-3">
        <label class="form-label" for="start_date">{{ __('Start date') }}</label>
        <input type="text" name="start_date" id="start_date" autocomplete="off" class="form-control datepicker" value="{{ $startDate }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="end_date">{{ __('End date') }}</label>
        <input type="text" name="end_date" id="end_date" autocomplete="off" class="form-control datepicker" value="{{ $endDate }}">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">{{ __('Apply') }}</button>
    </div>
</form>

<p class="text-muted small">{{ __('Based on debit/credit totals on designated cash-related accounts.') }}</p>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card metric-card success">
            <div class="card-body">
                <div class="text-muted small">{{ __('Cash inflows') }}</div>
                <div class="h4">${{ number_format($data['inflows'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card metric-card danger">
            <div class="card-body">
                <div class="text-muted small">{{ __('Cash outflows') }}</div>
                <div class="h4">${{ number_format($data['outflows'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card metric-card">
            <div class="card-body">
                <div class="text-muted small">{{ __('Net cash flow') }}</div>
                <div class="h4">{{ $data['net_cash_flow'] >= 0 ? '' : '-' }} ${{ number_format(abs($data['net_cash_flow']), 2) }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
