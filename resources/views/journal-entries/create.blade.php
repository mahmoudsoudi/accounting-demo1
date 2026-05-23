@extends('layouts.app')

@section('title', __('New Journal Entry'))

@section('content')
<div class="mb-4">
    <a href="{{ route('journal-entries.index') }}" class="text-decoration-none">&larr; {{ __('Back to journal entries') }}</a>
</div>

<h1 class="h3 mb-4">{{ __('New Journal Entry') }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('journal-entries.store') }}" method="POST" id="journal-entry-form">
            @csrf

            @php
                $lineKeys = collect(old('lines', []))->keys();
                $lineIndices = $lineKeys->isNotEmpty() ? $lineKeys->all() : [0, 1];
            @endphp

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label" for="transaction_date">{{ __('Transaction date') }} <span class="text-danger">*</span></label>
                    <input type="text" name="transaction_date" id="transaction_date" autocomplete="off"
                           class="form-control datepicker @error('transaction_date') is-invalid @enderror"
                           value="{{ old('transaction_date', now()->toDateString()) }}" required>
                    @error('transaction_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-8">
                    <label class="form-label" for="notes">{{ __('Notes') }}</label>
                    <textarea name="notes" id="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            @error('lines')<div class="alert alert-danger">{{ $message }}</div>@enderror
            @error('balance')<div class="alert alert-danger">{{ $message }}</div>@enderror

            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>{{ __('Lines') }}</strong>
                <button type="button" id="add-journal-line" class="btn btn-sm btn-outline-primary">{{ __('Add line') }}</button>
            </div>

            <div class="table-responsive">
                <table class="table table-sm align-middle" id="journal-lines-table">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Account') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th>{{ __('Debit') }}</th>
                            <th>{{ __('Credit') }}</th>
                            <th width="48"></th>
                        </tr>
                    </thead>
                    <tbody id="journal-lines-body">
                        @foreach($lineIndices as $idx)
                            @include('journal-entries._line_row', ['index' => $idx, 'line' => null, 'accounts' => $accounts])
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-end">{{ __('Totals') }}</th>
                            <th id="journal-total-debit">0.00</th>
                            <th id="journal-total-credit">0.00</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">{{ __('Save draft') }}</button>
                <a href="{{ route('journal-entries.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>

<template id="journal-line-template">
    @include('journal-entries._line_row', ['index' => '__INDEX__', 'line' => null, 'accounts' => $accounts])
</template>
@endsection

@push('scripts')
<script>
(function() {
    const body = document.getElementById('journal-lines-body');
    const template = document.getElementById('journal-line-template');
    const addBtn = document.getElementById('add-journal-line');

    function nextIndex() {
        const inputs = body.querySelectorAll('select[name^="lines["]');
        let max = -1;
        inputs.forEach(function(sel) {
            const m = sel.name.match(/^lines\[(\d+)\]/);
            if (m) max = Math.max(max, parseInt(m[1], 10));
        });
        return max + 1;
    }

    function recalcTotals() {
        let d = 0, c = 0;
        body.querySelectorAll('.journal-debit').forEach(function(el) {
            d += parseFloat(el.value || 0);
        });
        body.querySelectorAll('.journal-credit').forEach(function(el) {
            c += parseFloat(el.value || 0);
        });
        document.getElementById('journal-total-debit').textContent = d.toFixed(2);
        document.getElementById('journal-total-credit').textContent = c.toFixed(2);
    }

    function bindRow(row) {
        row.querySelectorAll('.journal-debit, .journal-credit').forEach(function(el) {
            el.addEventListener('input', recalcTotals);
        });
        row.querySelector('.remove-line')?.addEventListener('click', function() {
            if (body.querySelectorAll('.journal-line-row').length > 2) {
                row.remove();
                recalcTotals();
            }
        });
    }

    document.querySelectorAll('.journal-line-row').forEach(bindRow);
    recalcTotals();

    addBtn.addEventListener('click', function() {
        const html = template.innerHTML.replace(/__INDEX__/g, nextIndex());
        const wrap = document.createElement('tbody');
        wrap.innerHTML = html.trim();
        const row = wrap.querySelector('tr');
        body.appendChild(row);
        bindRow(row);
        recalcTotals();
    });
})();
</script>
@endpush
