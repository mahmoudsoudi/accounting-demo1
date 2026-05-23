@php $i = $index; @endphp
<tr class="journal-line-row">
    <td>
        <select name="lines[{{ $i }}][account_id]" class="form-select form-select-sm @error('lines.'.$i.'.account_id') is-invalid @enderror" required>
            <option value="">{{ __('Select account') }}</option>
            @foreach($accounts as $acc)
                <option value="{{ $acc->id }}" @selected(old('lines.'.$i.'.account_id', $line?->account_id) == $acc->id)>{{ $acc->full_name }}</option>
            @endforeach
        </select>
        @error('lines.'.$i.'.account_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </td>
    <td>
        <input type="text" name="lines[{{ $i }}][description]" class="form-control form-control-sm"
               value="{{ old('lines.'.$i.'.description', $line?->description) }}" placeholder="{{ __('Description') }}">
    </td>
    <td>
        <input type="number" step="0.01" min="0" name="lines[{{ $i }}][debit]" class="form-control form-control-sm journal-debit"
               value="{{ old('lines.'.$i.'.debit', $line?->debit ?? '') }}" placeholder="0.00">
    </td>
    <td>
        <input type="number" step="0.01" min="0" name="lines[{{ $i }}][credit]" class="form-control form-control-sm journal-credit"
               value="{{ old('lines.'.$i.'.credit', $line?->credit ?? '') }}" placeholder="0.00">
    </td>
    <td class="text-nowrap">
        <button type="button" class="btn btn-sm btn-outline-danger remove-line" title="{{ __('Remove line') }}">&times;</button>
    </td>
</tr>
