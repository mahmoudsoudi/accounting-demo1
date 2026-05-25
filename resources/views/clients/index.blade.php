@extends('layouts.app')

@section('title', __('Clients'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h1 class="h3 mb-0">{{ __('Clients') }}</h1>
    <a href="{{ route('clients.create') }}" class="btn btn-primary">{{ __('New Client') }}</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Phone') }}</th>
                    <th>{{ __('City') }}</th>
                    <th class="text-end">{{ __('Trust balance') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr class="align-middle" role="link" tabindex="0"
                    onclick="window.location='{{ route('clients.show', $client) }}'"
                    onkeydown="if (event.key === 'Enter') window.location='{{ route('clients.show', $client) }}'"
                    style="cursor: pointer;">
                    <td class="fw-medium">{{ $client->full_name }}</td>
                    <td>{{ $client->email ?? '—' }}</td>
                    <td>{{ $client->phone ?? '—' }}</td>
                    <td>{{ $client->city ?? '—' }}</td>
                    <td class="text-end"><x-accounting-amount :amount="$client->trust_balance ?? 0" :currency="false" /></td>
                    <td>
                        @if($client->is_active ?? true)
                            <span class="badge bg-success">{{ __('Active') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation()">{{ __('View') }}</a>
                        <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-outline-secondary" onclick="event.stopPropagation()">{{ __('Edit') }}</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">{{ __('No clients yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($clients->hasPages())
        <div class="card-footer">{{ $clients->links() }}</div>
    @endif
</div>
@endsection
