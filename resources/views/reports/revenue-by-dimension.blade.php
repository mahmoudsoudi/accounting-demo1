@extends('layouts.app')

@section('title', __('Revenue by Dimension'))

@section('content')
<div class="mb-4">
    <a href="{{ route('reports.index') }}" class="text-decoration-none">&larr; {{ __('Back to reports') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('Revenue by Dimension') }}</h1>
<p class="text-muted small">{{ __('Invoice totals for the selected period.') }}</p>

<form method="GET" action="{{ route('reports.revenue-by-dimension') }}" class="row g-3 align-items-end mb-4">
    <div class="col-md-3">
        <label class="form-label" for="start_date">{{ __('Start date') }}</label>
        <input type="text" name="start_date" id="start_date" autocomplete="off" class="form-control datepicker" value="{{ $startDate }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="end_date">{{ __('End date') }}</label>
        <input type="text" name="end_date" id="end_date" autocomplete="off" class="form-control datepicker" value="{{ $endDate }}">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">{{ __('Apply') }}</button>
    </div>
</form>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">{{ __('By client') }}</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Label') }}</th>
                            <th class="text-end">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($byClient as $row)
                        <tr>
                            <td>{{ $row->client?->full_name ?? __('Unassigned') }}</td>
                            <td class="text-end">${{ number_format((float) $row->total, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-muted py-4 text-center">{{ __('No data.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">{{ __('By property') }}</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Label') }}</th>
                            <th class="text-end">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($byProperty as $row)
                        <tr>
                            <td>{{ $row->property?->full_address ?? __('Unassigned') }}</td>
                            <td class="text-end">${{ number_format((float) $row->total, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-muted py-4 text-center">{{ __('No data.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">{{ __('By agent') }}</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Label') }}</th>
                            <th class="text-end">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($byAgent as $row)
                        <tr>
                            <td>{{ $row->agent?->full_name ?? __('Unassigned') }}</td>
                            <td class="text-end">${{ number_format((float) $row->total, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-muted py-4 text-center">{{ __('No data.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
