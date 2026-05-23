@extends('layouts.app')

@section('title', __('Record Payment'))

@section('content')
<div class="mb-4">
    <a href="{{ route('payments.index') }}" class="text-decoration-none">&larr; {{ __('Back to payments') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('Record payment') }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('payments.store') }}" method="POST">
            @csrf
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label" for="invoice_id">{{ __('Invoice') }} <span class="text-danger">*</span></label>
                    <select name="invoice_id" id="invoice_id" class="form-select @error('invoice_id') is-invalid @enderror" required>
                        <option value="">{{ __('Select invoice') }}</option>
                        @foreach($invoices as $invoice)
                            <option value="{{ $invoice->id }}" @selected((string) old('invoice_id', $invoiceId) === (string) $invoice->id)>
                                {{ $invoice->invoice_number }} · {{ $invoice->client?->full_name ?? __('Client') }}
                                · {{ $invoice->invoice_date->format('Y-m-d') }} · ${{ number_format($invoice->total, 2) }}
                            </option>
                        @endforeach
                    </select>
                    @error('invoice_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="payment_date">{{ __('Payment date') }} <span class="text-danger">*</span></label>
                    <input type="text" name="payment_date" id="payment_date" autocomplete="off"
                           class="form-control datepicker @error('payment_date') is-invalid @enderror"
                           value="{{ old('payment_date', now()->toDateString()) }}" required>
                    @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="amount">{{ __('Amount') }} <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror"
                           value="{{ old('amount') }}" required>
                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="payment_method">{{ __('Payment method') }} <span class="text-danger">*</span></label>
                    <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method->value }}" @selected(old('payment_method') === $method->value)>{{ $method->label() }}</option>
                        @endforeach
                    </select>
                    @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12">
                    <label class="form-label" for="reference">{{ __('Reference') }}</label>
                    <input type="text" name="reference" id="reference" class="form-control @error('reference') is-invalid @enderror" value="{{ old('reference') }}">
                    @error('reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label" for="notes">{{ __('Notes') }}</label>
                    <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">{{ __('Save payment') }}</button>
            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
        </form>
    </div>
</div>
@endsection
