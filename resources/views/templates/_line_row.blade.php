@php $i = $index; @endphp
<tr class="template-line-row">
    <td>
        <select name="lines[{{ $i }}][account_code]" class="form-select form-select-sm @error('lines.'.$i.'.account_code') is-invalid @enderror" required>
            <option value="">{{ __('Select account') }}</option>
            @foreach($accounts as $account)
                <option value="{{ $account->code }}" @selected(old('lines.'.$i.'.account_code', $line?->account_code) === $account->code)>
                    {{ $account->full_name }}
                </option>
            @endforeach
        </select>
        @error('lines.'.$i.'.account_code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </td>
    <td>
        <select name="lines[{{ $i }}][side]" class="form-select form-select-sm @error('lines.'.$i.'.side') is-invalid @enderror" required>
            <option value="debit" @selected(old('lines.'.$i.'.side', $line?->side) === 'debit')>{{ __('Debit') }}</option>
            <option value="credit" @selected(old('lines.'.$i.'.side', $line?->side) === 'credit')>{{ __('Credit') }}</option>
        </select>
        @error('lines.'.$i.'.side')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </td>
    <td>
        <select name="lines[{{ $i }}][amount_variable]" class="form-select form-select-sm @error('lines.'.$i.'.amount_variable') is-invalid @enderror" required>
            @foreach($amountVariables as $variable)
                <option value="{{ $variable }}" @selected(old('lines.'.$i.'.amount_variable', $line?->amount_variable) === $variable)>
                    {{ \App\Support\TemplateVariable::label($variable) }}
                </option>
            @endforeach
        </select>
        @error('lines.'.$i.'.amount_variable')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </td>
    <td>
        <input type="text" name="lines[{{ $i }}][description_template]" class="form-control form-control-sm @error('lines.'.$i.'.description_template') is-invalid @enderror"
               value="{{ old('lines.'.$i.'.description_template', $line?->description_template) }}" placeholder="{{ __('e.g.') }} {client}">
        @error('lines.'.$i.'.description_template')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </td>
    <td class="text-nowrap">
        <button type="button" class="btn btn-sm btn-outline-danger remove-template-line" title="{{ __('Remove line') }}">&times;</button>
    </td>
</tr>
