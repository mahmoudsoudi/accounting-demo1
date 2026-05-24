@extends('layouts.app')

@section('title', __('Commission Calculator'))

@section('content')
<div class="mb-4">
    <a href="{{ route('calculators.index') }}" class="text-decoration-none">&larr; {{ __('Calculators') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('Commission Calculator') }}</h1>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('calculators.commission') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="sale_price">{{ __('Sale price') }}</label>
                        <input type="number" step="1" min="1" name="sale_price" id="sale_price" class="form-control @error('sale_price') is-invalid @enderror"
                               value="{{ old('sale_price', 500000) }}" required>
                        @error('sale_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="commission_rate">{{ __('Commission rate (%)') }}</label>
                        <input type="number" step="0.01" min="0" name="commission_rate" id="commission_rate" class="form-control @error('commission_rate') is-invalid @enderror"
                               value="{{ old('commission_rate', '2.5') }}" required>
                        @error('commission_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="agent_split_rate">{{ __('Agent share (%)') }}</label>
                        <input type="number" step="0.01" min="0" max="100" name="agent_split_rate" id="agent_split_rate" class="form-control @error('agent_split_rate') is-invalid @enderror"
                               value="{{ old('agent_split_rate', '50') }}" required>
                        @error('agent_split_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="referral_fee">{{ __('Referral fee (flat)') }}</label>
                        <input type="number" step="0.01" min="0" name="referral_fee" id="referral_fee" class="form-control"
                               value="{{ old('referral_fee', 0) }}">
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('Calculate') }}</button>
                </form>
            </div>
        </div>
    </div>
    @if($result)
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ __('Sale price') }}</span><strong>${{ number_format($result['sale_price'], 2) }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ __('Total commission') }}</span><strong>${{ number_format($result['total_commission'], 2) }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ __('Referral fee') }}</span><strong>${{ number_format($result['referral_fee'], 2) }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between bg-light">
                        <span>{{ __('After referral') }}</span><strong>${{ number_format($result['total_commission'] - $result['referral_fee'], 2) }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ __('Agent amount (:pct%)', ['pct' => $result['agent_split_rate']]) }}</span><strong>${{ number_format($result['agent_amount'], 2) }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ __('Brokerage remainder') }}</span><strong>${{ number_format($result['brokerage_amount'], 2) }}</strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
