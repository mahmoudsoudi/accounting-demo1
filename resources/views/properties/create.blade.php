@extends('layouts.app')

@section('title', __('New Property'))

@section('content')
<div class="mb-4">
    <a href="{{ route('properties.index') }}" class="text-decoration-none">&larr; {{ __('Back to properties') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('New Property') }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('properties.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label" for="address">{{ __('Street address') }} <span class="text-danger">*</span></label>
                    <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}" required>
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-5">
                    <label class="form-label" for="city">{{ __('City') }} <span class="text-danger">*</span></label>
                    <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}" required>
                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="province">{{ __('Province') }} <span class="text-danger">*</span></label>
                    <input type="text" name="province" id="province" maxlength="2" class="form-control text-uppercase @error('province') is-invalid @enderror"
                           value="{{ old('province') }}" required placeholder="{{ __('e.g.') }} ON">
                    @error('province')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-5">
                    <label class="form-label" for="postal_code">{{ __('Postal code') }}</label>
                    <input type="text" name="postal_code" id="postal_code" class="form-control @error('postal_code') is-invalid @enderror" value="{{ old('postal_code') }}">
                    @error('postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="property_type">{{ __('Property type') }}</label>
                    <input type="text" name="property_type" id="property_type" class="form-control @error('property_type') is-invalid @enderror" value="{{ old('property_type') }}">
                    @error('property_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="client_id">{{ __('Listed client') }}</label>
                    <select name="client_id" id="client_id" class="form-select @error('client_id') is-invalid @enderror">
                        <option value="">{{ __('— None —') }}</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" @selected(old('client_id') == $c->id)>{{ $c->full_name }}</option>
                        @endforeach
                    </select>
                    @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="purchase_price">{{ __('Purchase price') }}</label>
                    <input type="number" step="0.01" name="purchase_price" id="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror" value="{{ old('purchase_price') }}">
                    @error('purchase_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="current_value">{{ __('Current value') }}</label>
                    <input type="number" step="0.01" name="current_value" id="current_value" class="form-control @error('current_value') is-invalid @enderror" value="{{ old('current_value') }}">
                    @error('current_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
