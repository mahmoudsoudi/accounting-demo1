@extends('layouts.app')

@section('title', __('Accounts Receivable Aging'))

@section('content')
<div class="mb-4">
    <a href="{{ route('reports.index') }}" class="text-decoration-none">&larr; {{ __('Back to reports') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('A/R Aging') }}</h1>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Bucket') }}</th>
                    <th class="text-end">{{ __('Open balance') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $r)
                <tr>
                    <td>{{ ucfirst($r->bucket) }}</td>
                    <td class="text-end">${{ number_format((float) $r->total, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="2" class="text-center text-muted py-4">{{ __('Nothing outstanding.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
