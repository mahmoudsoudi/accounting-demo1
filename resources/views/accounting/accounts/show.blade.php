@extends('layouts.app')

@section('title', $account->name)

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <h1>{{ $account->code }} - {{ $account->name }}</h1>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('accounting.accounts.edit', $account) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('accounting.accounts.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1"><strong>Type:</strong></p>
                <p class="mb-3"><span class="badge bg-info">{{ ucfirst($account->type) }}</span></p>

                <p class="text-muted mb-1"><strong>Normal Balance:</strong></p>
                <p class="mb-3"><span class="badge bg-secondary">{{ ucfirst($account->normal_balance) }}</span></p>

                <p class="text-muted mb-1"><strong>Status:</strong></p>
                <p class="mb-0">
                    @if ($account->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Journal Lines</h5>
            </div>
            @if ($account->journalLines->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Entry #</th>
                                <th>Description</th>
                                <th class="text-end">Debit</th>
                                <th class="text-end">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($account->journalLines->sortByDesc('created_at')->take(10) as $line)
                                <tr>
                                    <td>{{ $line->journalEntry->date->format('Y-m-d') }}</td>
                                    <td>{{ $line->journalEntry->entry_number }}</td>
                                    <td>{{ $line->journalEntry->description ?? $line->description ?? '-' }}</td>
                                    <td class="text-end">
                                        @if ($line->debit > 0)
                                            {{ number_format($line->debit, 2) }}
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($line->credit > 0)
                                            {{ number_format($line->credit, 2) }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="{{ route('accounting.reports.general-ledger', ['account_id' => $account->id]) }}" class="btn btn-sm btn-info">
                        View Full Ledger
                    </a>
                </div>
            @else
                <div class="card-body text-muted text-center py-3">
                    No journal lines for this account yet
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
