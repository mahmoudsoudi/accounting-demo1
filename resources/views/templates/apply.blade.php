@extends('layouts.app')

@section('title', __('Apply Template'))

@section('content')
@php
    use App\Support\TemplateVariable;

    $amountFields = collect($applicationVariables)->filter(fn ($variable) => TemplateVariable::isNumeric($variable))->values();
    $contextFields = collect($applicationVariables)->reject(fn ($variable) => TemplateVariable::isNumeric($variable))->values();
@endphp

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
                            <textarea name="notes" id="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $template->description) }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    @if($amountFields->isNotEmpty())
                        <h6 class="text-uppercase text-muted mb-3">{{ __('Amounts') }}</h6>
                        <div class="row g-3 mb-4">
                            @foreach($amountFields as $variable)
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label" for="{{ $variable }}">{{ TemplateVariable::label($variable) }}</label>
                                    <input type="number" step="0.01" min="0" name="{{ $variable }}" id="{{ $variable }}"
                                           class="form-control @error($variable) is-invalid @enderror"
                                           value="{{ old($variable) }}">
                                    @if($variable === TemplateVariable::COMMISSION_RATE)
                                        <div class="form-text">{{ __('Used to calculate agent amount when agent amount is left blank.') }}</div>
                                    @endif
                                    @if($variable === TemplateVariable::AMOUNT && $amountFields->contains(TemplateVariable::PRINCIPAL))
                                        <div class="form-text">{{ __('Leave blank to use principal + interest.') }}</div>
                                    @endif
                                    @error($variable)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($contextFields->isNotEmpty())
                        <h6 class="text-uppercase text-muted mb-3">{{ __('Context') }}</h6>
                        <div class="row g-3 mb-4">
                            @foreach($contextFields as $variable)
                                <div class="col-md-4">
                                    <label class="form-label" for="{{ $variable }}">{{ TemplateVariable::label($variable) }}</label>
                                    <input type="text" name="{{ $variable }}" id="{{ $variable }}"
                                           class="form-control @error($variable) is-invalid @enderror"
                                           value="{{ old($variable) }}">
                                    @error($variable)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary">{{ __('Create journal entry') }}</button>
                    <a href="{{ route('templates.show', $template) }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
