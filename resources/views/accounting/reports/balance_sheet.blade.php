@extends('layouts.app')

@section('title', 'Balance Sheet')

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <h1>Balance Sheet</h1>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label for="to" class="form-label">As of Date</label>
                <input type="date" name="to" id="to" class="form-control" value="{{ request('to', now()->toDateString()) }}">
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary w-100">Generate</button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Assets</h5>
            </div>
            @if (count($assets) > 0)
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        @foreach ($assets as $row)
                            <tr>
                                <td>{{ $row['account']->code }} - {{ $row['account']->name }}</td>
                                <td class="text-end">{{ $row['amount'] }}</td>
                            </tr>
                        @endforeach
                        <tr class="border-top fw-bold">
                            <td>Total Assets</td>
                            <td class="text-end">{{ $total_assets }}</td>
                        </tr>
                    </tbody>
                </table>
            @else
                <div class="card-body text-muted">No asset transactions</div>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h5 class="mb-0">Liabilities</h5>
            </div>
            @if (count($liabilities) > 0)
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        @foreach ($liabilities as $row)
                            <tr>
                                <td>{{ $row['account']->code }} - {{ $row['account']->name }}</td>
                                <td class="text-end">{{ $row['amount'] }}</td>
                            </tr>
                        @endforeach
                        <tr class="border-top fw-bold">
                            <td>Total Liabilities</td>
                            <td class="text-end">{{ $total_liabilities }}</td>
                        </tr>
                    </tbody>
                </table>
            @else
                <div class="card-body text-muted">No liability transactions</div>
            @endif
        </div>

        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Equity</h5>
            </div>
            @if (count($equity) > 0 || (float) $current_earnings !== 0)
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        @foreach ($equity as $row)
                            <tr>
                                <td>{{ $row['account']->code }} - {{ $row['account']->name }}</td>
                                <td class="text-end">{{ $row['amount'] }}</td>
                            </tr>
                        @endforeach
                        @if ((float) $current_earnings !== 0)
                            <tr>
                                <td>Current Earnings (P&L)</td>
                                <td class="text-end">{{ $current_earnings }}</td>
                            </tr>
                        @endif
                        <tr class="border-top fw-bold">
                            <td>Total Equity</td>
                            <td class="text-end">{{ $total_equity }}</td>
                        </tr>
                    </tbody>
                </table>
            @else
                <div class="card-body text-muted">No equity transactions</div>
            @endif
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5>Total Liabilities and Equity</h5>
                <p class="display-6">{{ $total_liabilities_and_equity }}</p>
            </div>
            <div class="col-md-6">
                @if ($is_balanced)
                    <div class="alert alert-success mb-0">
                        <h5 class="alert-heading">✓ Balance Sheet is Balanced</h5>
                        <p class="mb-0">Assets ({{ $total_assets }}) = Liabilities + Equity ({{ $total_liabilities_and_equity }})</p>
                    </div>
                @else
                    <div class="alert alert-danger mb-0">
                        <h5 class="alert-heading">✗ Balance Sheet is NOT Balanced</h5>
                        <p class="mb-0">Difference: {{ number_format(abs((float) $total_assets - (float) $total_liabilities_and_equity), 2) }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
