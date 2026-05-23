@extends('layouts.app')

@section('title', __('Apply Template'))

@section('content')
<div class="mb-4">
    <a href="{{ route('templates.show', $template) }}" class="text-decoration-none">&larr; {{ __('Back to template') }}</a>
</div>

<h1 class="h3 mb-2">{{ __('Apply template') }}</h1>
<p class="text-muted mb-4">{{ $template->name }}</p>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('templates.apply', $template) }}" method="POST">
                    @csrf

                    <p class="small text-muted mb-4">{{ __('Fill only the amounts and fields relevant to this template. Unused values are ignored.') }}</p>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label" for="transaction_date">{{ __('Transaction date') }}</label>
                            <input type="text" name="transaction_date" id="transaction_date" autocomplete="off"
                                   class="form-control datepicker @error('transaction_date') is-invalid @enderror"
                                   value="{{ old('transaction_date', now()->toDateString()) }}">
                            @error('transaction_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label" for="notes">{{ __('Notes') }}</label>
                            <textarea name="notes" id="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <h6 class="text-uppercase text-muted mb-3">{{ __('Amounts') }}</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label" for="amount">{{ __('Amount') }}</label>
                            <input type="number" step="0.01" min="0" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror"
                                   value="{{ old('amount') }}">
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label" for="principal">{{ __('Principal') }}</label>
                            <input type="number" step="0.01" min="0" name="principal" id="principal" class="form-control @error('principal') is-invalid @enderror"
                                   value="{{ old('principal') }}">
                            @error('principal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label" for="interest">{{ __('Interest') }}</label>
                            <input type="number" step="0.01" min="0" name="interest" id="interest" class="form-control @error('interest') is-invalid @enderror"
                                   value="{{ old('interest') }}">
                            @error('interest')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label" for="tax">{{ __('Tax') }}</label>
                            <input type="number" step="0.01" min="0" name="tax" id="tax" class="form-control @error('tax') is-invalid @enderror"
                                   value="{{ old('tax') }}">
                            @error('tax')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label" for="commission_rate">{{ __('Commission rate (%)') }}</label>
                            <input type="number" step="0.01" min="0" name="commission_rate" id="commission_rate" class="form-control @error('commission_rate') is-invalid @enderror"
                                   value="{{ old('commission_rate') }}">
                            @error('commission_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label" for="agent_amount">{{ __('Agent amount') }}</label>
                            <input type="number" step="0.01" min="0" name="agent_amount" id="agent_amount" class="form-control @error('agent_amount') is-invalid @enderror"
                                   value="{{ old('agent_amount') }}">
                            @error('agent_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <h6 class="text-uppercase text-muted mb-3">{{ __('Context') }}</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label" for="client">{{ __('Client') }}</label>
                            <input type="text" name="client" id="client" class="form-control @error('client') is-invalid @enderror" value="{{ old('client') }}">
                            @error('client')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="agent">{{ __('Agent') }}</label>
                            <input type="text" name="agent" id="agent" class="form-control @error('agent') is-invalid @enderror" value="{{ old('agent') }}">
                            @error('agent')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="property">{{ __('Property') }}</label>
                            <input type="text" name="property" id="property" class="form-control @error('property') is-invalid @enderror" value="{{ old('property') }}">
                            @error('property')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">{{ __('Create journal entry') }}</button>
                    <a href="{{ route('templates.show', $template) }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
