@extends('layouts.app')

@section('title', $entry->entry_number)

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <h1>{{ $entry->entry_number }}</h1>
    </div>
    <div class="col-md-4 text-end">
        @if ($entry->status === 'draft')
            <form action="{{ route('accounting.journal-entries.post', $entry) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-success">Post</button>
            </form>
        @elseif ($entry->status === 'posted' && !$entry->reversed_at)
            <form action="{{ route('accounting.journal-entries.reverse', $entry) }}" method="POST" style="display:inline;" onsubmit="return confirm('Reverse this entry?');">
                @csrf
                <button type="submit" class="btn btn-warning">Reverse</button>
            </form>
        @endif
        <a href="{{ route('accounting.journal-entries.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1"><strong>Date:</strong></p>
                <p class="mb-3">{{ $entry->date->format('Y-m-d') }}</p>

                <p class="text-muted mb-1"><strong>Status:</strong></p>
                <p class="mb-3">
                    @if ($entry->status === 'posted')
                        <span class="badge bg-success">Posted</span>
                    @elseif ($entry->status === 'draft')
                        <span class="badge bg-warning">Draft</span>
                    @else
                        <span class="badge bg-secondary">Reversed</span>
                    @endif
                </p>

                @if ($entry->posted_at)
                    <p class="text-muted mb-1"><strong>Posted At:</strong></p>
                    <p class="mb-0">{{ $entry->posted_at->format('Y-m-d H:i') }}</p>
                @endif

                @if ($entry->reversed_at)
                    <p class="text-muted mb-1"><strong>Reversed At:</strong></p>
                    <p class="mb-0">{{ $entry->reversed_at->format('Y-m-d H:i') }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Description</h5>
            </div>
            <div class="card-body">
                {{ $entry->description ?? 'No description' }}
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Journal Lines</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Account</th>
                            <th>Description</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalDebit = 0;
                            $totalCredit = 0;
                        @endphp
                        @foreach ($entry->lines as $line)
                            @php
                                $totalDebit += $line->debit;
                                $totalCredit += $line->credit;
                            @endphp
                            <tr>
                                <td><strong>{{ $line->account->code }}</strong> - {{ $line->account->name }}</td>
                                <td>{{ $line->description ?? '-' }}</td>
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
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="2" class="text-end"><strong>Totals:</strong></td>
                            <td class="text-end"><strong>{{ number_format($totalDebit, 2) }}</strong></td>
                            <td class="text-end"><strong>{{ number_format($totalCredit, 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
