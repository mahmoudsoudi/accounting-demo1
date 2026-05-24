@extends('layouts.app')

@section('title', __('Properties'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h1 class="h3 mb-0">{{ __('Properties') }}</h1>
    <a href="{{ route('properties.create') }}" class="btn btn-primary">{{ __('New Property') }}</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Address') }}</th>
                    <th>{{ __('City') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Owner') }}</th>
                    <th>{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($properties as $property)
                <tr>
                    <td>{{ $property->address }}</td>
                    <td>{{ $property->city }}, {{ $property->province }}</td>
                    <td>{{ $property->property_type ?? '—' }}</td>
                    <td>{{ $property->client?->full_name ?? '—' }}</td>
                    <td>
                        @if($property->is_active ?? true)
                            <span class="badge bg-success">{{ __('Active') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">{{ __('No properties.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($properties->hasPages())
        <div class="card-footer">{{ $properties->links() }}</div>
    @endif
</div>
@endsection
