@extends('layouts.app')

@section('title', 'Profit & Loss')

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <h1>Profit & Loss Report</h1>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label for="from" class="form-label">From Date</label>
                <input type="date" name="from" id="from" class="form-control" value="{{ request('from') }}">
            </div>
            <div class="col-md-4">
                <label for="to" class="form-label">To Date</label>
                <input type="date" name="to" id="to" class="form-control" value="{{ request('to') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Revenue</h5>
        @if (count($revenues) > 0)
            <table class="table table-sm table-borderless mb-3">
                <tbody>
                    @foreach ($revenues as $row)
                        <tr>
                            <td>{{ $row['account']->code }} - {{ $row['account']->name }}</td>
                            <td class="text-end">{{ $row['amount'] }}</td>
                        </tr>
                    @endforeach
                    <tr class="border-top">
                        <td><strong>Total Revenue</strong></td>
                        <td class="text-end"><strong>{{ $total_revenue }}</strong></td>
                    </tr>
                </tbody>
            </table>
        @else
            <p class="text-muted">No revenue transactions for the selected period</p>
        @endif

        <hr>

        <h5 class="card-title">Expenses</h5>
        @if (count($expenses) > 0)
            <table class="table table-sm table-borderless mb-3">
                <tbody>
                    @foreach ($expenses as $row)
                        <tr>
                            <td>{{ $row['account']->code }} - {{ $row['account']->name }}</td>
                            <td class="text-end">{{ $row['amount'] }}</td>
                        </tr>
                    @endforeach
                    <tr class="border-top">
                        <td><strong>Total Expenses</strong></td>
                        <td class="text-end"><strong>{{ $total_expense }}</strong></td>
                    </tr>
                </tbody>
            </table>
        @else
            <p class="text-muted">No expense transactions for the selected period</p>
        @endif

        <hr>

        <table class="table table-sm table-borderless">
            <tbody>
                <tr class="border-top border-bottom">
                    <td><h5 class="mb-0">Net Profit / (Loss)</h5></td>
                    <td class="text-end">
                        <h5 class="mb-0 @if ((float) $net_profit >= 0) text-success @else text-danger @endif">
                            {{ $net_profit }}
                        </h5>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
