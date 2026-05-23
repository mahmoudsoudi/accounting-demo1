@extends('layouts.app')

@section('title', __('Income Statement'))

@section('content')
<div class="mb-4">
    <a href="{{ route('reports.index') }}" class="text-decoration-none">&larr; {{ __('Back to reports') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('Income Statement') }}</h1>

<form method="GET" action="{{ route('reports.income-statement') }}" class="row g-3 align-items-end mb-4">
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

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header fw-semibold">{{ __('Revenue') }}</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <tbody>
                        @foreach($data['revenue'] as $row)
                        <tr>
                            <td>{{ $row['account']->full_name }}</td>
                            <td class="text-end">${{ number_format($row['balance'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light"><tr><th>{{ __('Total revenue') }}</th><th class="text-end">${{ number_format($data['total_revenue'], 2) }}</th></tr></tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header fw-semibold">{{ __('Cost of Sales') }}</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <tbody>
                        @foreach($data['cost_of_sales'] as $row)
                        <tr>
                            <td>{{ $row['account']->full_name }}</td>
                            <td class="text-end">${{ number_format($row['balance'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light"><tr><th>{{ __('Total cost of sales') }}</th><th class="text-end">${{ number_format($data['total_cost_of_sales'], 2) }}</th></tr></tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header fw-semibold">{{ __('Operating Expenses') }}</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <tbody>
                        @foreach($data['expenses'] as $row)
                        <tr>
                            <td>{{ $row['account']->full_name }}</td>
                            <td class="text-end">${{ number_format($row['balance'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light"><tr><th>{{ __('Total expenses') }}</th><th class="text-end">${{ number_format($data['total_expenses'], 2) }}</th></tr></tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body d-flex justify-content-between fs-5">
        <span>{{ __('Net income') }}</span>
        <strong class="{{ $data['net_income'] >= 0 ? 'text-success' : 'text-danger' }}">${{ number_format($data['net_income'], 2) }}</strong>
    </div>
</div>
@endsection
