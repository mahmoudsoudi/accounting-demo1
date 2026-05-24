<tr class="invoice-line-row">
    <td>
        <input type="text" name="lines[{{ $index }}][description]" class="form-control form-control-sm @error('lines.'.$index.'.description') is-invalid @enderror"
               value="{{ old('lines.'.$index.'.description', $line?->description) }}" required placeholder="{{ __('Description') }}">
        @error('lines.'.$index.'.description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </td>
    <td>
        <input type="number" step="0.01" min="0" name="lines[{{ $index }}][quantity]" class="form-control form-control-sm invoice-qty"
               value="{{ old('lines.'.$index.'.quantity', $line?->quantity ?? 1) }}" required>
        @error('lines.'.$index.'.quantity')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </td>
    <td>
        <input type="number" step="0.01" min="0" name="lines[{{ $index }}][unit_price]" class="form-control form-control-sm invoice-price"
               value="{{ old('lines.'.$index.'.unit_price', $line?->unit_price) }}" required placeholder="0.00">
        @error('lines.'.$index.'.unit_price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </td>
    <td class="invoice-line-subtotal pt-3 text-muted small">{{ __('Line total') }}</td>
    <td>
        <select name="lines[{{ $index }}][account_id]" class="form-select form-select-sm @error('lines.'.$index.'.account_id') is-invalid @enderror">
            <option value="">{{ __('Account (optional)') }}</option>
            @foreach($accounts as $acc)
                <option value="{{ $acc->id }}" @selected(old('lines.'.$index.'.account_id', $line?->account_id) == $acc->id)>{{ $acc->full_name }}</option>
            @endforeach
        </select>
    </td>
    <td><button type="button" class="btn btn-sm btn-outline-danger remove-invoice-line" title="{{ __('Remove') }}">&times;</button></td>
</tr>
