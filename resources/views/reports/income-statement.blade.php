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

@php
    $summaryRows = [
        ['label' => __('Total revenue'), 'amount' => $data['total_revenue']],
        ['label' => __('Total cost of sales'), 'amount' => -$data['total_cost_of_sales']],
        ['label' => __('Gross profit'), 'amount' => $data['gross_profit'], 'emphasis' => true],
        ['label' => __('Total operating expenses'), 'amount' => -$data['total_expenses']],
        ['label' => __('EBITDA'), 'amount' => $data['ebitda'], 'emphasis' => true],
        ['label' => __('Depreciation expense'), 'amount' => -$data['total_depreciation_expense']],
        ['label' => __('Amortization expense'), 'amount' => -$data['total_amortization_expense']],
        ['label' => __('EBIT'), 'amount' => $data['ebit'], 'emphasis' => true],
        ['label' => __('Interest expense'), 'amount' => -$data['total_interest_expense']],
        ['label' => __('Earnings before tax (EBT)'), 'amount' => $data['ebt'], 'emphasis' => true],
        ['label' => __('Income tax expense'), 'amount' => -$data['total_income_tax_expense']],
        ['label' => __('Net income after tax'), 'amount' => $data['net_income_after_tax'], 'emphasis' => true, 'final' => true],
    ];
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header fw-semibold">{{ __('Revenue') }}</div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                @forelse($data['revenue'] as $row)
                                <tr>
                                    <td>{{ $row['account']->full_name }}</td>
                                    <td class="text-end">${{ number_format($row['balance'], 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="2" class="text-muted">{{ __('No activity') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header fw-semibold">{{ __('Cost of Sales') }}</div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                @forelse($data['cost_of_sales'] as $row)
                                <tr>
                                    <td>{{ $row['account']->full_name }}</td>
                                    <td class="text-end">${{ number_format($row['balance'], 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="2" class="text-muted">{{ __('No activity') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header fw-semibold">{{ __('Operating Expenses') }}</div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                @forelse($data['expenses'] as $row)
                                <tr>
                                    <td>{{ $row['account']->full_name }}</td>
                                    <td class="text-end">${{ number_format($row['balance'], 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="2" class="text-muted">{{ __('No activity') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header fw-semibold">{{ __('Below EBITDA') }}</div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                @foreach([
                                    __('Interest expense') => $data['interest_expense'],
                                    __('Depreciation expense') => $data['depreciation_expense'],
                                    __('Amortization expense') => $data['amortization_expense'],
                                    __('Income tax expense') => $data['income_tax_expense'],
                                ] as $sectionLabel => $rows)
                                    @foreach($rows as $row)
                                    <tr>
                                        <td>{{ $row['account']->full_name }}</td>
                                        <td class="text-end">${{ number_format($row['balance'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                @endforeach
                                @if(
                                    $data['interest_expense']->isEmpty()
                                    && $data['depreciation_expense']->isEmpty()
                                    && $data['amortization_expense']->isEmpty()
                                    && $data['income_tax_expense']->isEmpty()
                                )
                                <tr><td colspan="2" class="text-muted">{{ __('No activity') }}</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header fw-semibold">{{ __('Income Statement Summary') }}</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <tbody>
                        @foreach($summaryRows as $row)
                        <tr @class(['table-light' => !empty($row['emphasis']), 'border-top' => !empty($row['final'])])>
                            <td @class(['fw-semibold' => !empty($row['emphasis']) || !empty($row['final'])])>{{ $row['label'] }}</td>
                            <td @class(['text-end', 'fw-semibold' => !empty($row['emphasis']) || !empty($row['final']), 'text-success' => $row['amount'] >= 0, 'text-danger' => $row['amount'] < 0])>
                                ${{ number_format($row['amount'], 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
