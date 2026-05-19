@extends('layouts.app')

@section('title', 'General Ledger')

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <h1>General Ledger</h1>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label for="account_id" class="form-label">Select Account</label>
                <select name="account_id" id="account_id" class="form-select" required>
                    <option value="">-- Select an Account --</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}" @selected(request('account_id') == $account->id)>
                            {{ $account->code }} - {{ $account->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="from" class="form-label">From Date</label>
                <input type="date" name="from" id="from" class="form-control" value="{{ request('from') }}">
            </div>
            <div class="col-md-3">
                <label for="to" class="form-label">To Date</label>
                <input type="date" name="to" id="to" class="form-control" value="{{ request('to') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

@if (isset($account) && isset($lines))
    <div class="card">
        <div class="card-header bg-light">
            <div class="row">
                <div class="col-md-6">
                    <h5>{{ $account->code }} - {{ $account->name }}</h5>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0 text-muted">Type: <strong>{{ ucfirst($account->type) }}</strong></p>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Entry #</th>
                        <th>Description</th>
                        <th class="text-end">Debit</th>
                        <th class="text-end">Credit</th>
                        <th class="text-end">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lines as $line)
                        <tr>
                            <td>{{ $line['date'] }}</td>
                            <td>{{ $line['entry_number'] }}</td>
                            <td>{{ $line['description'] }}</td>
                            <td class="text-end">{{ $line['debit'] }}</td>
                            <td class="text-end">{{ $line['credit'] }}</td>
                            <td class="text-end"><strong>{{ $line['running_balance'] }}</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">No ledger entries for this account</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            <div class="row">
                <div class="col-md-3">
                    <p class="mb-0"><strong>Opening Balance:</strong> {{ $opening_balance }}</p>
                </div>
                <div class="col-md-3">
                    <p class="mb-0"><strong>Closing Balance:</strong> {{ $closing_balance }}</p>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info">Select an account to view its general ledger.</div>
@endif
@endsection
