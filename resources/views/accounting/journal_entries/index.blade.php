@extends('layouts.app')

@section('title', 'Journal Entries')

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <h1>Journal Entries</h1>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('accounting.journal-entries.create') }}" class="btn btn-primary">
            + New Entry
        </a>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Entry #</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th class="text-end">Debit</th>
                    <th class="text-end">Credit</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($entries as $entry)
                    @php
                        $totalDebit = $entry->lines->sum('debit');
                        $totalCredit = $entry->lines->sum('credit');
                    @endphp
                    <tr>
                        <td><strong>{{ $entry->entry_number }}</strong></td>
                        <td>{{ $entry->date->format('Y-m-d') }}</td>
                        <td>{{ $entry->description ?? '-' }}</td>
                        <td>
                            @if ($entry->status === 'posted')
                                <span class="badge bg-success">Posted</span>
                            @elseif ($entry->status === 'draft')
                                <span class="badge bg-warning">Draft</span>
                            @else
                                <span class="badge bg-secondary">Reversed</span>
                            @endif
                        </td>
                        <td class="text-end">{{ number_format($totalDebit, 2) }}</td>
                        <td class="text-end">{{ number_format($totalCredit, 2) }}</td>
                        <td>
                            <a href="{{ route('accounting.journal-entries.show', $entry) }}" class="btn btn-sm btn-info">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">No journal entries found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($entries->hasPages())
        <div class="card-footer">
            {{ $entries->links() }}
        </div>
    @endif
</div>
@endsection
