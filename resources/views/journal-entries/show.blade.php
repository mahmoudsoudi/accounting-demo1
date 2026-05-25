@extends('layouts.app')

@php
    use App\Enums\JournalEntryStatus;
@endphp

@section('title', __('Journal Entry'))

@section('content')
<div class="mb-4 d-flex flex-wrap justify-content-between gap-3 align-items-center">
    <a href="{{ route('journal-entries.index') }}" class="text-decoration-none">&larr; {{ __('Back to journal entries') }}</a>
    <div class="btn-group flex-wrap">
        @if($entry->status === JournalEntryStatus::Draft && Gate::allows('create-accounting'))
            <a href="{{ route('journal-entries.edit', $entry) }}" class="btn btn-outline-secondary">{{ __('Edit') }}</a>
        @endif
        @if($entry->status === JournalEntryStatus::Draft && Gate::allows('create-accounting'))
            <form action="{{ route('journal-entries.post', $entry) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-primary" @if(!$entry->isBalanced()) disabled title="{{ __('Entry must balance') }}" @endif>{{ __('Post') }}</button>
            </form>
        @endif
        @if(in_array($entry->status, [JournalEntryStatus::Posted, JournalEntryStatus::Draft], true) && Gate::allows('approve-journal-entry', $entry))
            <form action="{{ route('journal-entries.approve', $entry) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success">{{ __('Approve') }}</button>
            </form>
        @endif
        @if($entry->status === JournalEntryStatus::Approved && Gate::allows('reverse-transactions'))
            <form action="{{ route('journal-entries.reverse', $entry) }}" method="POST" class="d-inline"
                  onsubmit="return confirm(@json(__('Reverse this approved entry?')));">
                @csrf
                <button type="submit" class="btn btn-outline-danger">{{ __('Reverse') }}</button>
            </form>
        @endif
    </div>
</div>

<div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">{{ $entry->reference_number }}</h1>
        <span class="badge bg-secondary">{{ $entry->status->label() }}</span>
        <span class="text-muted ms-2">{{ $entry->transaction_date->format('Y-m-d') }}</span>
        @if($entry->referenceable)
            <div class="text-muted small mt-1">
                {{ __('Source') }}: {{ class_basename($entry->referenceable) }} #{{ $entry->referenceable->getKey() }}
            </div>
        @endif
    </div>
</div>

@error('balance')<div class="alert alert-danger">{{ $message }}</div>@enderror
@error('approve')<div class="alert alert-danger">{{ $message }}</div>@enderror
@error('reverse')<div class="alert alert-danger">{{ $message }}</div>@enderror

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">{{ __('Lines') }}</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Account') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th>{{ __('Reference') }}</th>
                            <th class="text-end">{{ __('Debit') }}</th>
                            <th class="text-end">{{ __('Credit') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entry->lines as $line)
                        <tr>
                            <td>{{ $line->account?->full_name ?? '—' }}</td>
                            <td>{{ $line->description ?? '—' }}</td>
                            <td>
                                @if($line->referenceable)
                                    {{ class_basename($line->referenceable) }} #{{ $line->referenceable->getKey() }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($line->debit, 2) }}</td>
                            <td class="text-end">{{ number_format($line->credit, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end">{{ __('Totals') }}</th>
                            <th class="text-end">{{ number_format($entry->total_debits, 2) }}</th>
                            <th class="text-end">{{ number_format($entry->total_credits, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @if(!$entry->isBalanced())
                <div class="card-footer text-warning">{{ __('This entry does not balance.') }}</div>
            @endif
        </div>

        @if($entry->notes)
        <div class="card mb-4">
            <div class="card-header">{{ __('Notes') }}</div>
            <div class="card-body"><p class="mb-0">{{ $entry->notes }}</p></div>
        </div>
        @endif

        @if($entry->originalEntry || $entry->reversalEntry->isNotEmpty())
        <div class="card">
            <div class="card-header">{{ __('Related entries') }}</div>
            <ul class="list-group list-group-flush">
                @if($entry->originalEntry)
                    <li class="list-group-item">
                        {{ __('Reversal of') }}:
                        <a href="{{ route('journal-entries.show', $entry->originalEntry) }}">{{ $entry->originalEntry->reference_number }}</a>
                    </li>
                @endif
                @foreach($entry->reversalEntry as $rev)
                    <li class="list-group-item">
                        {{ __('Reversal') }}:
                        <a href="{{ route('journal-entries.show', $rev) }}">{{ $rev->reference_number }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">{{ __('Audit trail') }}</div>
            <ul class="list-group list-group-flush small">
                <li class="list-group-item">{{ __('Created by') }}: {{ $entry->creator ? __($entry->creator->name) : '—' }}</li>
                @if($entry->posted_at)
                    <li class="list-group-item">{{ __('Posted at') }}: {{ $entry->posted_at->format('Y-m-d H:i') }}</li>
                @endif
                @if($entry->approver)
                    <li class="list-group-item">{{ __('Approved by') }}: {{ __($entry->approver->name) }}</li>
                    <li class="list-group-item">{{ __('Approved at') }}: {{ $entry->approved_at?->format('Y-m-d H:i') }}</li>
                @endif
                @if($entry->reverser)
                    <li class="list-group-item">{{ __('Reversed by') }}: {{ __($entry->reverser->name) }}</li>
                    <li class="list-group-item">{{ __('Reversed at') }}: {{ $entry->reversed_at?->format('Y-m-d H:i') }}</li>
                @endif
            </ul>
        </div>

        @if($entry->auditLogs->isNotEmpty())
        <div class="card">
            <div class="card-header">{{ __('Activity') }}</div>
            <div class="list-group list-group-flush small">
                @foreach($entry->auditLogs->sortByDesc('created_at') as $log)
                    <div class="list-group-item">
                        <div class="fw-semibold">{{ $log->action }}</div>
                        <div class="text-muted">{{ $log->user?->name ?? '—' }} · {{ $log->created_at?->format('Y-m-d H:i') }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
