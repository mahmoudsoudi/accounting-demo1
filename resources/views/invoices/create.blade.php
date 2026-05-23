@extends('layouts.app')

@section('title', __('New Invoice'))

@section('content')
<div class="mb-4">
    <a href="{{ route('invoices.index') }}" class="text-decoration-none">&larr; {{ __('Back to invoices') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('New Invoice') }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('invoices.store') }}" method="POST">
            @csrf

            <div class="row g-3 mb-4">
                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="client_id">{{ __('Client') }}</label>
                    <select name="client_id" id="client_id" class="form-select @error('client_id') is-invalid @enderror">
                        <option value="">{{ __('— Optional —') }}</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" @selected(old('client_id') == $c->id)>{{ $c->full_name }}</option>
                        @endforeach
                    </select>
                    @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                    <label class="form-label" for="invoice_date">{{ __('Invoice date') }} <span class="text-danger">*</span></label>
                    <input type="text" name="invoice_date" id="invoice_date" autocomplete="off"
                           class="form-control datepicker @error('invoice_date') is-invalid @enderror"
                           value="{{ old('invoice_date', now()->toDateString()) }}" required>
                    @error('invoice_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="due_date">{{ __('Due date') }}</label>
                    <input type="text" name="due_date" id="due_date" autocomplete="off"
                           class="form-control datepicker @error('due_date') is-invalid @enderror"
                           value="{{ old('due_date') }}">
                    @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="tax_amount">{{ __('Tax amount') }}</label>
                    <input type="number" step="0.01" min="0" name="tax_amount" id="tax_amount" class="form-control invoice-tax-input @error('tax_amount') is-invalid @enderror"
                           value="{{ old('tax_amount', 0) }}">
                    @error('tax_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label" for="notes">{{ __('Notes') }}</label>
                    <textarea name="notes" id="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                </div>
            </div>

            @error('lines')<div class="alert alert-danger">{{ $message }}</div>@enderror

            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>{{ __('Line items') }}</strong>
                <button type="button" id="add-invoice-line" class="btn btn-sm btn-outline-primary">{{ __('Add line') }}</button>
            </div>

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Description') }}</th>
                            <th>{{ __('Qty') }}</th>
                            <th>{{ __('Unit price') }}</th>
                            <th>{{ __('Line total') }}</th>
                            <th>{{ __('Account') }}</th>
                            <th width="48"></th>
                        </tr>
                    </thead>
                    <tbody id="invoice-lines-body">
                        @php
                            if (is_array(old('lines'))) {
                                $lineIndices = collect(old('lines'))->keys()->all();
                            } else {
                                $lineIndices = range(0, 0);
                            }
                        @endphp
                        @foreach($lineIndices as $idx)
                            @include('invoices._line_row', ['index' => $idx, 'line' => null, 'accounts' => $accounts])
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row justify-content-end mt-3 mb-4">
                <div class="col-md-6 col-lg-4">
                    <div class="d-flex justify-content-between"><span>{{ __('Subtotal') }}</span><strong id="invoice-subtotal">0.00</strong></div>
                    <div class="d-flex justify-content-between mt-2"><span>{{ __('Tax') }}</span><strong id="invoice-tax-display">{{ number_format(old('tax_amount', 0), 2) }}</strong></div>
                    <hr>
                    <div class="d-flex justify-content-between h5"><span>{{ __('Total') }}</span><strong id="invoice-total">0.00</strong></div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">{{ __('Save invoice') }}</button>
            <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
        </form>
    </div>
</div>

<template id="invoice-line-template">
    @include('invoices._line_row', ['index' => '__INDEX__', 'line' => null, 'accounts' => $accounts])
</template>
@endsection

@push('scripts')
<script>
(function() {
    const body = document.getElementById('invoice-lines-body');
    const template = document.getElementById('invoice-line-template');
    const taxInput = document.getElementById('tax_amount');

    function nextIndex() {
        let max = -1;
        body.querySelectorAll('input[name^="lines["]').forEach(function(el) {
            const m = el.name.match(/^lines\[(\d+)\]/);
            if (m) max = Math.max(max, parseInt(m[1], 10));
        });
        return max + 1;
    }

    function lineSubtotal(row) {
        const q = parseFloat(row.querySelector('.invoice-qty')?.value || 0);
        const p = parseFloat(row.querySelector('.invoice-price')?.value || 0);
        const el = row.querySelector('.invoice-line-subtotal');
        if (el) el.textContent = (q * p).toFixed(2);
        return q * p;
    }

    function recalc() {
        let sub = 0;
        body.querySelectorAll('.invoice-line-row').forEach(function(row) { sub += lineSubtotal(row); });
        const tax = parseFloat(taxInput?.value || 0);
        document.getElementById('invoice-subtotal').textContent = sub.toFixed(2);
        document.getElementById('invoice-tax-display').textContent = tax.toFixed(2);
        document.getElementById('invoice-total').textContent = (sub + tax).toFixed(2);
    }

    function bindRow(row) {
        row.querySelectorAll('.invoice-qty, .invoice-price').forEach(function(el) {
            el.addEventListener('input', recalc);
        });
        row.querySelector('.remove-invoice-line')?.addEventListener('click', function() {
            if (body.querySelectorAll('.invoice-line-row').length > 1) {
                row.remove();
                recalc();
            }
        });
    }

    document.querySelectorAll('.invoice-line-row').forEach(bindRow);
    taxInput?.addEventListener('input', recalc);
    recalc();

    document.getElementById('add-invoice-line').addEventListener('click', function() {
        const html = template.innerHTML.replace(/__INDEX__/g, nextIndex());
        const wrap = document.createElement('tbody');
        wrap.innerHTML = html.trim();
        const row = wrap.querySelector('tr');
        body.appendChild(row);
        bindRow(row);
        recalc();
    });
})();
</script>
@endpush
