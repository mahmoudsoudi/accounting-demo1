@extends('layouts.app')

@section('title', __('Journal Entries'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h1 class="h3 mb-0">{{ __('Journal Entries') }}</h1>
    @can('create-accounting')
        <a href="{{ route('journal-entries.create') }}" class="btn btn-primary">{{ __('New Journal Entry') }}</a>
    @endcan
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Reference') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Lines') }}</th>
                    <th>{{ __('Created By') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                <tr>
                    <td>{{ $entry->reference_number }}</td>
                    <td>{{ $entry->transaction_date->format('Y-m-d') }}</td>
                    <td><span class="badge bg-secondary">{{ $entry->status->label() }}</span></td>
                    <td>{{ $entry->lines->count() }}</td>
                    <td>{{ $entry->creator ? __($entry->creator->name) : '—' }}</td>
                    <td class="text-end">
                        <a href="{{ route('journal-entries.show', $entry) }}" class="btn btn-sm btn-outline-primary">{{ __('View') }}</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">{{ __('No journal entries.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($entries->hasPages())
        <div class="card-footer">{{ $entries->links() }}</div>
    @endif
</div>
@endsection
