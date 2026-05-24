@extends('layouts.app')

@section('title', __('Mortgage Calculator'))

@section('content')
<div class="mb-4">
    <a href="{{ route('calculators.index') }}" class="text-decoration-none">&larr; {{ __('Calculators') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('Mortgage Calculator') }}</h1>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('calculators.mortgage') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="principal">{{ __('Principal amount') }}</label>
                        <input type="number" step="1" min="1" name="principal" id="principal" class="form-control @error('principal') is-invalid @enderror"
                               value="{{ old('principal', 350000) }}" required>
                        @error('principal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="annual_rate">{{ __('Annual rate (%)') }}</label>
                        <input type="number" step="0.001" min="0" name="annual_rate" id="annual_rate" class="form-control @error('annual_rate') is-invalid @enderror"
                               value="{{ old('annual_rate', '5.5') }}" required>
                        @error('annual_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="term_months">{{ __('Term (months)') }}</label>
                        <input type="number" step="1" min="1" name="term_months" id="term_months" class="form-control @error('term_months') is-invalid @enderror"
                               value="{{ old('term_months', 300) }}" required>
                        @error('term_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('Calculate') }}</button>
                </form>
            </div>
        </div>
    </div>

    @if($result)
    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-body row g-3">
                <div class="col-md-4">
                    <div class="text-muted small">{{ __('Monthly payment') }}</div>
                    <div class="h4">${{ number_format($result['monthly_payment'], 2) }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">{{ __('Total interest') }}</div>
                    <div class="h4">${{ number_format($result['total_interest'], 2) }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">{{ __('Total paid') }}</div>
                    <div class="h4">${{ number_format($result['total_paid'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">{{ __('Schedule (first payments)') }}</div>
            <div class="table-responsive" style="max-height:420px;">
                <table class="table table-sm mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>#</th>
                            <th class="text-end">{{ __('Payment') }}</th>
                            <th class="text-end">{{ __('Principal') }}</th>
                            <th class="text-end">{{ __('Interest') }}</th>
                            <th class="text-end">{{ __('Balance') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(array_slice($result['schedule'], 0, 60) as $row)
                        <tr>
                            <td>{{ $row['payment_number'] }}</td>
                            <td class="text-end">${{ number_format($row['payment_amount'], 2) }}</td>
                            <td class="text-end">${{ number_format($row['principal_amount'], 2) }}</td>
                            <td class="text-end">${{ number_format($row['interest_amount'], 2) }}</td>
                            <td class="text-end">${{ number_format($row['remaining_balance'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(count($result['schedule']) > 60)
                <div class="card-footer text-muted small">{{ __('Showing first 60 rows.') }}</div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
