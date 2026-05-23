@extends('layouts.app')

@section('title', __('Balance Sheet'))

@section('content')
<div class="mb-4">
    <a href="{{ route('reports.index') }}" class="text-decoration-none">&larr; {{ __('Back to reports') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('Balance Sheet') }}</h1>

<form method="GET" action="{{ route('reports.balance-sheet') }}" class="row g-3 align-items-end mb-4">
    <div class="col-md-4">
        <label class="form-label" for="as_of">{{ __('As of') }}</label>
        <input type="text" name="as_of" id="as_of" autocomplete="off" class="form-control datepicker" value="{{ $asOf }}">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">{{ __('Apply') }}</button>
    </div>
</form>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header fw-semibold">{{ __('Assets') }}</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <tbody>
                        @foreach($data['assets'] as $row)
                        <tr>
                            <td>{{ $row['account']->full_name }}</td>
                            <td class="text-end">${{ number_format($row['balance'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light"><tr><th>{{ __('Total assets') }}</th><th class="text-end">${{ number_format($data['total_assets'], 2) }}</th></tr></tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header fw-semibold">{{ __('Liabilities') }}</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <tbody>
                        @foreach($data['liabilities'] as $row)
                        <tr>
                            <td>{{ $row['account']->full_name }}</td>
                            <td class="text-end">${{ number_format($row['balance'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light"><tr><th>{{ __('Total liabilities') }}</th><th class="text-end">${{ number_format($data['total_liabilities'], 2) }}</th></tr></tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header fw-semibold">{{ __('Equity') }}</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <tbody>
                        @foreach($data['equity'] as $row)
                        <tr>
                            <td>{{ $row['account']->full_name }}</td>
                            <td class="text-end">${{ number_format($row['balance'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light"><tr><th>{{ __('Total equity') }}</th><th class="text-end">${{ number_format($data['total_equity'], 2) }}</th></tr></tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body d-flex justify-content-between">
        <span>{{ __('Total liabilities + equity') }}</span>
        <strong>${{ number_format($data['total_liabilities'] + $data['total_equity'], 2) }}</strong>
    </div>
</div>
@endsection
