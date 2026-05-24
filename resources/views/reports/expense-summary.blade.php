@extends('layouts.app')

@section('title', __('Expense Summary'))

@section('content')
<div class="mb-4">
    <a href="{{ route('reports.index') }}" class="text-decoration-none">&larr; {{ __('Back to reports') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('Expense Summary') }}</h1>
<p class="text-muted small">{{ __('Approved expenses only.') }}</p>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Category') }}</th>
                    <th class="text-end">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($summary as $row)
                <tr>
                    <td>{{ $row->category ?: __('Uncategorized') }}</td>
                    <td class="text-end">${{ number_format((float) $row->total, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="2" class="text-center text-muted py-4">{{ __('No approved expenses.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
