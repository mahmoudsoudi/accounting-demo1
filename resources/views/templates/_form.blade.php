@php
    $templateModel = $template ?? null;
    $selectedContext = collect(old('context_variables', $templateModel?->variables ?? []))
        ->filter(fn ($variable) => in_array($variable, $contextVariables, true))
        ->values()
        ->all();
    $lineKeys = collect(old('lines', []))->keys();
    $lineIndices = $lineKeys->isNotEmpty()
        ? $lineKeys->all()
        : ($templateModel?->lines->isNotEmpty()
            ? $templateModel->lines->pluck('id')->all()
            : [0, 1]);
@endphp

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label" for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $templateModel?->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="slug">{{ __('Slug') }} <span class="text-danger">*</span></label>
        <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror"
               value="{{ old('slug', $templateModel?->slug) }}" required maxlength="255" pattern="[A-Za-z0-9_-]+">
        <div class="form-text">{{ __('Unique identifier used by the system. Use letters, numbers, dashes, or underscores.') }}</div>
        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="trigger_action_type">{{ __('Trigger type') }} <span class="text-danger">*</span></label>
        <select name="trigger_action_type" id="trigger_action_type" class="form-select @error('trigger_action_type') is-invalid @enderror" required>
            @foreach($triggerTypes as $value => $label)
                <option value="{{ $value }}" @selected(old('trigger_action_type', $templateModel?->trigger_action_type ?? 'custom') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('trigger_action_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 d-flex align-items-end">
        <div class="form-check">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="is_active" class="form-check-input"
                   @checked(old('is_active', $templateModel?->is_active ?? true))>
            <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label" for="description">{{ __('Description') }}</label>
        <textarea name="description" id="description" rows="2" class="form-control @error('description') is-invalid @enderror">{{ old('description', $templateModel?->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mb-4">
    <label class="form-label d-block">{{ __('Optional context fields when applying') }}</label>
    <div class="row g-2">
        @foreach($contextVariables as $variable)
            <div class="col-md-3">
                <div class="form-check">
                    <input type="checkbox" name="context_variables[]" value="{{ $variable }}" id="context_{{ $variable }}"
                           class="form-check-input" @checked(in_array($variable, $selectedContext, true))>
                    <label class="form-check-label" for="context_{{ $variable }}">{{ \App\Support\TemplateVariable::label($variable) }}</label>
                </div>
            </div>
        @endforeach
    </div>
    <div class="form-text">{{ __('These appear on the apply form and can be used in line descriptions as placeholders, e.g. {client}.') }}</div>
    @error('context_variables')<div class="text-danger small">{{ $message }}</div>@enderror
</div>

@error('lines')<div class="alert alert-danger">{{ $message }}</div>@enderror

<div class="d-flex justify-content-between align-items-center mb-2">
    <strong>{{ __('Journal lines') }}</strong>
    <button type="button" id="add-template-line" class="btn btn-sm btn-outline-primary">{{ __('Add line') }}</button>
</div>

<div class="table-responsive">
    <table class="table table-sm align-middle" id="template-lines-table">
        <thead class="table-light">
            <tr>
                <th>{{ __('Account') }}</th>
                <th>{{ __('Side') }}</th>
                <th>{{ __('Amount variable') }}</th>
                <th>{{ __('Description template') }}</th>
                <th width="48"></th>
            </tr>
        </thead>
        <tbody id="template-lines-body">
            @foreach($lineIndices as $idx)
                @php
                    $lineData = null;
                    if ($templateModel && ! old('lines')) {
                        $lineData = $templateModel->lines->firstWhere('id', $idx);
                    }
                @endphp
                @include('templates._line_row', [
                    'index' => $idx,
                    'line' => $lineData,
                    'accounts' => $accounts,
                    'amountVariables' => $amountVariables,
                ])
            @endforeach
        </tbody>
    </table>
</div>

<p class="small text-muted mb-0">{{ __('Each template needs at least two lines. Use the same amount variable on multiple lines when they share an amount (e.g. debit and credit for a simple payment).') }}</p>

<template id="template-line-template">
    @include('templates._line_row', [
        'index' => '__INDEX__',
        'line' => null,
        'accounts' => $accounts,
        'amountVariables' => $amountVariables,
    ])
</template>

@push('scripts')
<script>
(function() {
    const body = document.getElementById('template-lines-body');
    const template = document.getElementById('template-line-template');
    const addBtn = document.getElementById('add-template-line');
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    let slugEdited = slugInput.value.length > 0;

    slugInput.addEventListener('input', function() {
        slugEdited = slugInput.value.trim().length > 0;
    });

    nameInput?.addEventListener('input', function() {
        if (!slugEdited) {
            slugInput.value = nameInput.value
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }
    });

    function nextIndex() {
        const inputs = body.querySelectorAll('select[name^="lines["]');
        let max = -1;
        inputs.forEach(function(sel) {
            const match = sel.name.match(/^lines\[(\d+)\]/);
            if (match) {
                max = Math.max(max, parseInt(match[1], 10));
            }
        });
        return max + 1;
    }

    function bindRow(row) {
        row.querySelector('.remove-template-line')?.addEventListener('click', function() {
            if (body.querySelectorAll('.template-line-row').length > 2) {
                row.remove();
            }
        });
    }

    body.querySelectorAll('.template-line-row').forEach(bindRow);

    addBtn.addEventListener('click', function() {
        const html = template.innerHTML.replace(/__INDEX__/g, nextIndex());
        const wrap = document.createElement('tbody');
        wrap.innerHTML = html.trim();
        const row = wrap.querySelector('tr');
        body.appendChild(row);
        bindRow(row);
    });
})();
</script>
@endpush
