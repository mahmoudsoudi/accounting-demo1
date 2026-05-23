@extends('layouts.app')

@section('title', __('Agents'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h1 class="h3 mb-0">{{ __('Agents') }}</h1>
    <a href="{{ route('agents.create') }}" class="btn btn-primary">{{ __('New Agent') }}</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Phone') }}</th>
                    <th>{{ __('License') }}</th>
                    <th class="text-end">{{ __('Commission %') }}</th>
                    <th>{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($agents as $agent)
                <tr>
                    <td>{{ $agent->full_name }}</td>
                    <td>{{ $agent->email ?? '—' }}</td>
                    <td>{{ $agent->phone ?? '—' }}</td>
                    <td>{{ $agent->license_number ?? '—' }}</td>
                    <td class="text-end">{{ $agent->commission_rate !== null ? number_format((float) $agent->commission_rate * 100, 2).' %' : '—' }}</td>
                    <td>
                        @if($agent->is_active ?? true)
                            <span class="badge bg-success">{{ __('Active') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">{{ __('No agents yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($agents->hasPages())
        <div class="card-footer">{{ $agents->links() }}</div>
    @endif
</div>
@endsection
