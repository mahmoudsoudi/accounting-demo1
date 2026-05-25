@extends('layouts.app')

@section('title', __('Chart of Accounts'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">{{ __('Chart of Accounts') }}</h1>
        <p class="text-muted small mb-0">{{ __('Trial balance amounts for :start – :end', ['start' => $fiscalStart, 'end' => $fiscalEnd]) }}</p>
    </div>
    <a href="{{ route('accounts.create') }}" class="btn btn-primary">{{ __('New Account') }}</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Code') }}</th>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Normal Balance') }}</th>
                    <th>{{ __('Parent') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th class="text-end">{{ __('Debit') }}</th>
                    <th class="text-end">{{ __('Credit') }}</th>
                    <th class="text-end">{{ __('Balance') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accounts as $account)
                @php
                    $balance = $balances[$account->id] ?? ['debit' => 0, 'credit' => 0, 'balance' => 0];
                @endphp
                <tr class="align-middle" role="link" tabindex="0"
                    onclick="window.location='{{ route('accounts.show', $account) }}'"
                    onkeydown="if (event.key === 'Enter') window.location='{{ route('accounts.show', $account) }}'"
                    style="cursor: pointer;">
                    <td><code>{{ $account->code }}</code></td>
                    <td class="fw-medium">{{ $account->name }}</td>
                    <td>{{ $account->account_type->label() }}</td>
                    <td>{{ $account->normal_balance->label() }}</td>
                    <td>
                        @if($account->parent)
                            <a href="{{ route('accounts.show', $account->parent) }}" class="text-decoration-none" onclick="event.stopPropagation()">
                                {{ $account->parent->full_name }}
                            </a>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if($account->is_active)
                            <span class="badge bg-success">{{ __('Active') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                        @endif
                    </td>
                    <td class="text-end">{{ number_format($balance['debit'], 2) }}</td>
                    <td class="text-end">{{ number_format($balance['credit'], 2) }}</td>
                    <td class="text-end"><x-accounting-amount :amount="$balance['balance']" :currency="false" /></td>
                    <td class="text-end">
                        <a href="{{ route('accounts.show', $account) }}" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation()">{{ __('View') }}</a>
                        <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm btn-outline-secondary" onclick="event.stopPropagation()">{{ __('Edit') }}</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">{{ __('No accounts yet.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
