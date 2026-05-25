@extends('layouts.app')

@section('title', __('Trial Balance'))

@section('content')
<div class="mb-4">
    <a href="{{ route('reports.index') }}" class="text-decoration-none">&larr; {{ __('Back to reports') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('Trial Balance') }}</h1>

<form method="GET" action="{{ route('reports.trial-balance') }}" class="row g-3 align-items-end mb-4">
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

<div class="card">
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Account') }}</th>
                    <th class="text-end">{{ __('Debit') }}</th>
                    <th class="text-end">{{ __('Credit') }}</th>
                    <th class="text-end">{{ __('Balance') }}</th>
                </tr>
            </thead>
            <tbody>
                @php $totalDr = $rows->sum('debit'); $totalCr = $rows->sum('credit'); @endphp
                @forelse($rows as $row)
                <tr>
                    <td>
                        <a href="{{ route('accounts.show', $row['account']) }}" class="text-decoration-none">
                            {{ $row['account']->full_name }}
                        </a>
                    </td>
                    <td class="text-end">{{ number_format($row['debit'], 2) }}</td>
                    <td class="text-end">{{ number_format($row['credit'], 2) }}</td>
                    <td class="text-end"><x-accounting-amount :amount="$row['balance']" :currency="false" /></td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">{{ __('No balances for selected date.') }}</td></tr>
                @endforelse
            </tbody>
            @if($rows->isNotEmpty())
            <tfoot class="table-light">
                <tr>
                    <th>{{ __('Totals') }}</th>
                    <th class="text-end">{{ number_format($totalDr, 2) }}</th>
                    <th class="text-end">{{ number_format($totalCr, 2) }}</th>
                    <th></th>
                </tr>
                <tr @class(['table-success' => abs($totalDr - $totalCr) < 0.01, 'table-danger' => abs($totalDr - $totalCr) >= 0.01])>
                    <th colspan="4" class="text-center">
                        @if(abs($totalDr - $totalCr) < 0.01)
                            {{ __('Trial balance is balanced.') }}
                        @else
                            {{ __('Trial balance is not balanced.') }}
                        @endif
                    </th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
