@extends('layouts.app')

@section('title', __('Rental NOI Calculator'))

@section('content')
<div class="mb-4">
    <a href="{{ route('calculators.index') }}" class="text-decoration-none">&larr; {{ __('Calculators') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('Rental NOI Calculator') }}</h1>

<div class="row g-4">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('calculators.rental') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="monthly_rent">{{ __('Monthly rent') }}</label>
                            <input type="number" step="0.01" min="0" name="monthly_rent" id="monthly_rent" class="form-control @error('monthly_rent') is-invalid @enderror"
                                   value="{{ old('monthly_rent', 2500) }}" required>
                            @error('monthly_rent')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="vacancy_rate">{{ __('Vacancy rate %') }}</label>
                            <input type="number" step="0.1" min="0" name="vacancy_rate" id="vacancy_rate" class="form-control"
                                   value="{{ old('vacancy_rate', '5') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="management_fee_rate">{{ __('Management fee %') }}</label>
                            <input type="number" step="0.1" min="0" name="management_fee_rate" id="management_fee_rate" class="form-control"
                                   value="{{ old('management_fee_rate', '8') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="maintenance_rate">{{ __('Maintenance reserve %') }}</label>
                            <input type="number" step="0.1" min="0" name="maintenance_rate" id="maintenance_rate" class="form-control"
                                   value="{{ old('maintenance_rate', '5') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="insurance">{{ __('Insurance (annual)') }}</label>
                            <input type="number" step="0.01" min="0" name="insurance" id="insurance" class="form-control" value="{{ old('insurance', 0) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="taxes">{{ __('Taxes (annual)') }}</label>
                            <input type="number" step="0.01" min="0" name="taxes" id="taxes" class="form-control" value="{{ old('taxes', 0) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="mortgage">{{ __('Mortgage (annual)') }}</label>
                            <input type="number" step="0.01" min="0" name="mortgage" id="mortgage" class="form-control" value="{{ old('mortgage', 0) }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label" for="other_expenses">{{ __('Other expenses (annual)') }}</label>
                            <input type="number" step="0.01" min="0" name="other_expenses" id="other_expenses" class="form-control" value="{{ old('other_expenses', 0) }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">{{ __('Calculate') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @if($result)
    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr><th>{{ __('Gross annual income') }}</th><td class="text-end">${{ number_format($result['gross_annual_income'], 2) }}</td></tr>
                        <tr><th>{{ __('Vacancy loss') }}</th><td class="text-end">${{ number_format($result['vacancy_loss'], 2) }}</td></tr>
                        <tr><th>{{ __('Effective gross income') }}</th><td class="text-end">${{ number_format($result['effective_gross_income'], 2) }}</td></tr>
                        <tr class="table-light"><td colspan="2">{{ __('Expenses') }}</td></tr>
                        <tr><th>{{ __('Management fee') }}</th><td class="text-end">${{ number_format($result['management_fee'], 2) }}</td></tr>
                        <tr><th>{{ __('Maintenance reserve') }}</th><td class="text-end">${{ number_format($result['maintenance'], 2) }}</td></tr>
                        <tr><th>{{ __('Insurance') }}</th><td class="text-end">${{ number_format($result['insurance'], 2) }}</td></tr>
                        <tr><th>{{ __('Taxes') }}</th><td class="text-end">${{ number_format($result['taxes'], 2) }}</td></tr>
                        <tr><th>{{ __('Mortgage') }}</th><td class="text-end">${{ number_format($result['mortgage'], 2) }}</td></tr>
                        <tr><th>{{ __('Other expenses') }}</th><td class="text-end">${{ number_format($result['other_expenses'], 2) }}</td></tr>
                        <tr><th>{{ __('Total expenses') }}</th><td class="text-end">${{ number_format($result['total_expenses'], 2) }}</td></tr>
                        <tr class="table-success"><th>{{ __('Net operating income') }}</th><td class="text-end fw-bold">${{ number_format($result['net_operating_income'], 2) }}</td></tr>
                        <tr class="table-primary"><th>{{ __('Monthly cash flow') }}</th><td class="text-end fw-bold">${{ number_format($result['monthly_cash_flow'], 2) }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
