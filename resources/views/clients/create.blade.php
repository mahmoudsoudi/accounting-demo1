@extends('layouts.app')

@section('title', __('New Client'))

@section('content')
<div class="mb-4">
    <a href="{{ route('clients.index') }}" class="text-decoration-none">&larr; {{ __('Back to clients') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('New Client') }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('clients.store') }}" method="POST">
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
                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
                </div>
                <div class="col-12">
                    <label class="form-label" for="address">{{ __('Address') }}</label>
                    <input type="text" name="address" id="address" class="form-control" value="{{ old('address') }}">
                </div>
                <div class="col-md-5">
                    <label class="form-label" for="city">{{ __('City') }}</label>
                    <input type="text" name="city" id="city" class="form-control" value="{{ old('city') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="province">{{ __('Province / state') }}</label>
                    <input type="text" name="province" id="province" maxlength="2" class="form-control text-uppercase @error('province') is-invalid @enderror"
                           value="{{ old('province') }}" placeholder="{{ __('e.g.') }} QC">
                    @error('province')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-5">
                    <label class="form-label" for="postal_code">{{ __('Postal code') }}</label>
                    <input type="text" name="postal_code" id="postal_code" class="form-control @error('postal_code') is-invalid @enderror" value="{{ old('postal_code') }}">
                    @error('postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
