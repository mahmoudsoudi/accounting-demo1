@extends('layouts.app')

@section('title', __('Calculators'))

@section('content')
<h1 class="h3 mb-4">{{ __('Calculators') }}</h1>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5">{{ __('Mortgage') }}</h2>
                <p class="text-muted small">{{ __('Payment estimate and amortization schedule.') }}</p>
                <a href="{{ route('calculators.mortgage') }}" class="btn btn-outline-primary">{{ __('Open') }}</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5">{{ __('Rental NOI') }}</h2>
                <p class="text-muted small">{{ __('Effective gross income, expenses, NOI, monthly cash flow.') }}</p>
                <a href="{{ route('calculators.rental') }}" class="btn btn-outline-primary">{{ __('Open') }}</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5">{{ __('Commission') }}</h2>
                <p class="text-muted small">{{ __('Splits after referral.') }}</p>
                <a href="{{ route('calculators.commission') }}" class="btn btn-outline-primary">{{ __('Open') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection
