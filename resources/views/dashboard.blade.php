@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h1>Dashboard</h1>
        <p class="text-muted">Welcome to your accounting system</p>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Total Accounts</h5>
                <p class="card-text display-4">{{ $totalAccounts }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Posted Entries</h5>
                <p class="card-text display-4">{{ $postedEntries }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Draft Entries</h5>
                <p class="card-text display-4">{{ $draftEntries }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Trial Balance</h5>
                @if ($isBalanced)
                    <span class="badge bg-success p-2 fs-6">Balanced</span>
                @else
                    <span class="badge bg-danger p-2 fs-6">Not Balanced</span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Links</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('accounting.accounts.create') }}" class="btn btn-primary btn-sm w-100">
                            Create Account
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('accounting.journal-entries.create') }}" class="btn btn-primary btn-sm w-100">
                            New Journal Entry
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('accounting.reports.trial-balance') }}" class="btn btn-info btn-sm w-100">
                            Trial Balance
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('accounting.reports.balance-sheet') }}" class="btn btn-info btn-sm w-100">
                            Balance Sheet
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
