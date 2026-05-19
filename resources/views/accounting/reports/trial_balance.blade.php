@extends('layouts.app')

@section('title', 'Trial Balance')

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <h1>Trial Balance</h1>
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
    <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
            <thead>
                <tr>
                    <th>Account Code</th>
                    <th>Account Name</th>
                    <th class="text-end">Debit Balance</th>
                    <th class="text-end">Credit Balance</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        <td><strong>{{ $row['account']->code }}</strong></td>
                        <td>{{ $row['account']->name }}</td>
                        <td class="text-end">{{ $row['debit_balance'] }}</td>
                        <td class="text-end">{{ $row['credit_balance'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">No transactions found for the selected period</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="table-light">
                    <td colspan="2" class="text-end"><strong>Totals:</strong></td>
                    <td class="text-end"><strong>{{ $total_debit }}</strong></td>
                    <td class="text-end"><strong>{{ $total_credit }}</strong></td>
                </tr>
                <tr @if ($is_balanced) class="table-success" @else class="table-danger" @endif>
                    <td colspan="4" class="text-center">
                        @if ($is_balanced)
                            <strong class="text-success">✓ Trial Balance is Balanced</strong>
                        @else
                            <strong class="text-danger">✗ Trial Balance is NOT Balanced</strong>
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
