@php
    $accountModel = $account ?? null;
@endphp

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label" for="code">{{ __('Code') }} <span class="text-danger">*</span></label>
        <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror"
               value="{{ old('code', $accountModel?->code) }}" required maxlength="20">
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-8">
        <label class="form-label" for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $accountModel?->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="account_type">{{ __('Account type') }} <span class="text-danger">*</span></label>
        <select name="account_type" id="account_type" class="form-select @error('account_type') is-invalid @enderror" required>
            @foreach($types as $type)
                <option value="{{ $type->value }}" @selected(old('account_type', $accountModel?->account_type?->value) === $type->value)>{{ $type->label() }}</option>
            @endforeach
        </select>
        @error('account_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="normal_balance">{{ __('Normal balance') }} <span class="text-danger">*</span></label>
        <select name="normal_balance" id="normal_balance" class="form-select @error('normal_balance') is-invalid @enderror" required>
            @foreach($balances as $balance)
                <option value="{{ $balance->value }}" @selected(old('normal_balance', $accountModel?->normal_balance?->value) === $balance->value)>{{ ucfirst($balance->value) }}</option>
            @endforeach
        </select>
        @error('normal_balance')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="parent_id">{{ __('Parent account') }}</label>
        <select name="parent_id" id="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
            <option value="">{{ __('— None —') }}</option>
            @foreach($parents as $p)
                <option value="{{ $p->id }}" @selected((string) old('parent_id', $accountModel?->parent_id) === (string) $p->id)>{{ $p->full_name }}</option>
            @endforeach
        </select>
        @error('parent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 d-flex align-items-end">
        <div class="form-check">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="is_active" class="form-check-input"
                   @checked(old('is_active', $accountModel?->is_active ?? true))>
            <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label" for="description">{{ __('Description') }}</label>
        <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $accountModel?->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
