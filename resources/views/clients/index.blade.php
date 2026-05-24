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
                    <th>{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr>
                    <td>{{ $client->full_name }}</td>
                    <td>{{ $client->email ?? '—' }}</td>
                    <td>{{ $client->phone ?? '—' }}</td>
                    <td>{{ $client->city ?? '—' }}</td>
                    <td>
                        @if($client->is_active ?? true)
                            <span class="badge bg-success">{{ __('Active') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">{{ __('No clients yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($clients->hasPages())
        <div class="card-footer">{{ $clients->links() }}</div>
    @endif
</div>
@endsection
