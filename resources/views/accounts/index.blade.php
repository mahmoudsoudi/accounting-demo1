@extends('layouts.app')

@section('title', __('Chart of Accounts'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ __('Chart of Accounts') }}</h1>
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
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accounts as $account)
                <tr>
                    <td><code>{{ $account->code }}</code></td>
                    <td>{{ $account->name }}</td>
                    <td>{{ $account->account_type->label() }}</td>
                    <td class="text-capitalize">{{ $account->normal_balance->value }}</td>
                    <td>{{ $account->parent?->full_name ?? '—' }}</td>
                    <td>
                        @if($account->is_active)
                            <span class="badge bg-success">{{ __('Active') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm btn-outline-secondary">{{ __('Edit') }}</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">{{ __('No accounts yet.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
