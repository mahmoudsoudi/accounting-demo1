@extends('layouts.app')

@section('title', __('New Expense'))

@section('content')
<div class="mb-4">
    <a href="{{ route('expenses.index') }}" class="text-decoration-none">&larr; {{ __('Back to expenses') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('New Expense') }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('expenses.store') }}" method="POST">
            @csrf
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label" for="vendor_name">{{ __('Vendor') }} <span class="text-danger">*</span></label>
                    <input type="text" name="vendor_name" id="vendor_name" class="form-control @error('vendor_name') is-invalid @enderror"
                           value="{{ old('vendor_name') }}" required>
                    @error('vendor_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="expense_date">{{ __('Expense date') }} <span class="text-danger">*</span></label>
                    <input type="text" name="expense_date" id="expense_date" autocomplete="off"
                           class="form-control datepicker @error('expense_date') is-invalid @enderror"
                           value="{{ old('expense_date', now()->toDateString()) }}" required>
                    @error('expense_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="property_id">{{ __('Property') }}</label>
                    <select name="property_id" id="property_id" class="form-select @error('property_id') is-invalid @enderror">
                        <option value="">{{ __('— Optional —') }}</option>
                        @foreach($properties as $p)
                            <option value="{{ $p->id }}" @selected(old('property_id') == $p->id)>{{ $p->full_address }}</option>
                        @endforeach
                    </select>
                    @error('property_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="agent_id">{{ __('Agent') }}</label>
                    <select name="agent_id" id="agent_id" class="form-select @error('agent_id') is-invalid @enderror">
                        <option value="">{{ __('— Optional —') }}</option>
                        @foreach($agents as $a)
                            <option value="{{ $a->id }}" @selected(old('agent_id') == $a->id)>{{ $a->full_name }}</option>
                        @endforeach
                    </select>
                    @error('agent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="category">{{ __('Category') }}</label>
                    <input type="text" name="category" id="category" class="form-control @error('category') is-invalid @enderror" value="{{ old('category') }}">
                    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="amount">{{ __('Amount') }} <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror"
                           value="{{ old('amount') }}" required>
                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="tax_amount">{{ __('Tax amount') }}</label>
                    <input type="number" step="0.01" min="0" name="tax_amount" id="tax_amount" class="form-control @error('tax_amount') is-invalid @enderror"
                           value="{{ old('tax_amount', 0) }}">
                    @error('tax_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="payment_method">{{ __('Payment method') }}</label>
                    <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror">
                        <option value="">{{ __('— Journal template —') }}</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method->value }}" @selected(old('payment_method') === $method->value)>{{ $method->label() }}</option>
                        @endforeach
                    </select>
                    @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">{{ __('Cash, bank, and credit card may post an automatic journal template when configured.') }}</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="account_id">{{ __('Expense account') }}</label>
                    <select name="account_id" id="account_id" class="form-select @error('account_id') is-invalid @enderror">
                        <option value="">{{ __('— Optional —') }}</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" @selected(old('account_id') == $acc->id)>{{ $acc->full_name }}</option>
                        @endforeach
                    </select>
                    @error('account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label" for="notes">{{ __('Notes') }}</label>
                    <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">{{ __('Save expense') }}</button>
            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
        </form>
    </div>
</div>
@endsection
