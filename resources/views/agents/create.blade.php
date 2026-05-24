@extends('layouts.app')

@section('title', __('New Agent'))

@section('content')
<div class="mb-4">
    <a href="{{ route('agents.index') }}" class="text-decoration-none">&larr; {{ __('Back to agents') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('New Agent') }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('agents.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="first_name">{{ __('First name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="last_name">{{ __('Last name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="email">{{ __('Email') }}</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="phone">{{ __('Phone') }}</label>
                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-8">
                    <label class="form-label" for="license_number">{{ __('License number') }}</label>
                    <input type="text" name="license_number" id="license_number" class="form-control @error('license_number') is-invalid @enderror" value="{{ old('license_number') }}">
                    @error('license_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="commission_rate">{{ __('Default commission (0–1)') }} <span class="text-danger">*</span></label>
                    <input type="number" step="0.0001" min="0" max="1" name="commission_rate" id="commission_rate" class="form-control @error('commission_rate') is-invalid @enderror"
                           value="{{ old('commission_rate') }}" placeholder="{{ __('e.g.') }} 0.025" required>
                    @error('commission_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">{{ __('Stored as decimal; 0.03 = 3%.') }}</small>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                <a href="{{ route('agents.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
