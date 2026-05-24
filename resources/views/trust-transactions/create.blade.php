@extends('layouts.app')

@section('title', __('New Trust Transaction'))

@section('content')
<div class="mb-4">
    <a href="{{ route('trust-transactions.index') }}" class="text-decoration-none">&larr; {{ __('Back to trust transactions') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('New Trust Transaction') }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('trust-transactions.store') }}" method="POST">
            @csrf
            <div class="row g-3 mb-4">
                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="transaction_type">{{ __('Transaction type') }} <span class="text-danger">*</span></label>
                    <select name="transaction_type" id="transaction_type" class="form-select @error('transaction_type') is-invalid @enderror" required>
                        <option value="">{{ __('— Select —') }}</option>
                        @foreach($transactionTypes as $type)
                            <option value="{{ $type->value }}" @selected(old('transaction_type') === $type->value)>{{ $type->label() }}</option>
                        @endforeach
                    </select>
                    @error('transaction_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="transaction_date">{{ __('Transaction date') }} <span class="text-danger">*</span></label>
                    <input type="text" name="transaction_date" id="transaction_date" autocomplete="off"
                           class="form-control datepicker @error('transaction_date') is-invalid @enderror"
                           value="{{ old('transaction_date', now()->toDateString()) }}" required>
                    @error('transaction_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="amount">{{ __('Amount') }} <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror"
                           value="{{ old('amount') }}" required>
                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="client_id">{{ __('Client') }}</label>
                    <select name="client_id" id="client_id" class="form-select @error('client_id') is-invalid @enderror">
                        <option value="">{{ __('— Optional —') }}</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>{{ $client->full_name }}</option>
                        @endforeach
                    </select>
                    @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="property_id">{{ __('Property') }}</label>
                    <select name="property_id" id="property_id" class="form-select @error('property_id') is-invalid @enderror">
                        <option value="">{{ __('— Optional —') }}</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" @selected(old('property_id') == $property->id)>{{ $property->full_address }}</option>
                        @endforeach
                    </select>
                    @error('property_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="payment_method">{{ __('Payment method') }}</label>
                    <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror">
                        <option value="">{{ __('— Optional —') }}</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method->value }}" @selected(old('payment_method') === $method->value)>{{ $method->label() }}</option>
                        @endforeach
                    </select>
                    @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="reference">{{ __('Reference') }}</label>
                    <input type="text" name="reference" id="reference" class="form-control @error('reference') is-invalid @enderror"
                           value="{{ old('reference') }}">
                    @error('reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label" for="notes">{{ __('Notes') }}</label>
                    <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">{{ __('Save trust transaction') }}</button>
            <a href="{{ route('trust-transactions.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
        </form>
    </div>
</div>
@endsection
