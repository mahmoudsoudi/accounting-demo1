@extends('layouts.app')

@section('title', $trustTransaction->trust_number)

@section('content')
<div class="mb-4">
    <a href="{{ route('trust-transactions.index') }}" class="text-decoration-none">&larr; {{ __('Back to trust transactions') }}</a>
</div>

<div class="d-flex flex-wrap justify-content-between gap-3 align-items-start mb-4">
    <div>
        <h1 class="h3 mb-2">{{ __('Trust Transaction') }} {{ $trustTransaction->trust_number }}</h1>
        <span class="badge bg-secondary">{{ $trustTransaction->transaction_type->label() }}</span>
        <span class="text-muted ms-2">{{ $trustTransaction->transaction_date->format('Y-m-d') }}</span>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">{{ __('Details') }}</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>{{ __('Client') }}</strong><br>{{ $trustTransaction->client?->full_name ?? '—' }}</li>
                <li class="list-group-item"><strong>{{ __('Property') }}</strong><br>{{ $trustTransaction->property?->full_address ?? '—' }}</li>
                <li class="list-group-item"><strong>{{ __('Payment method') }}</strong><br>{{ $trustTransaction->payment_method?->label() ?? '—' }}</li>
                <li class="list-group-item"><strong>{{ __('Reference') }}</strong><br>{{ $trustTransaction->reference ?? '—' }}</li>
                <li class="list-group-item"><strong>{{ __('Amount') }}</strong><br>${{ number_format($trustTransaction->amount, 2) }}</li>
            </ul>
        </div>

        @if($trustTransaction->notes)
            <div class="card">
                <div class="card-header">{{ __('Notes') }}</div>
                <div class="card-body"><p class="mb-0">{{ $trustTransaction->notes }}</p></div>
            </div>
        @endif
    </div>

    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">{{ __('Workflow') }}</div>
            <ul class="list-group list-group-flush small">
                <li class="list-group-item">{{ __('Created by') }}: {{ $trustTransaction->creator?->name }}</li>
            </ul>
        </div>

        <div class="card">
            <div class="card-header">{{ __('Journal entry') }}</div>
            <div class="card-body">
                @if($trustTransaction->journalEntry)
                    <a href="{{ route('journal-entries.show', $trustTransaction->journalEntry) }}">{{ $trustTransaction->journalEntry->reference_number }}</a>
                    <span class="badge bg-secondary ms-1">{{ $trustTransaction->journalEntry->status->label() }}</span>
                @else
                    <span class="text-muted">{{ __('None') }}</span>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
