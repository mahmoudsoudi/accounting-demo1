@php
    $accountModel = $account ?? null;
    $locales = $locales ?? config('locales.supported');
    $localeLabels = $localeLabels ?? config('locales.labels');
    $nameTranslations = old('name_translations', $accountModel?->name_translations ?? ['en' => $accountModel?->getRawOriginal('name') ?? '']);
    $descriptionTranslations = old('description_translations', $accountModel?->description_translations ?? ['en' => $accountModel?->getRawOriginal('description') ?? '']);
@endphp

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label" for="code">{{ __('Code') }} <span class="text-danger">*</span></label>
        <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror"
               value="{{ old('code', $accountModel?->code) }}" required maxlength="20">
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-8">
        <label class="form-label">{{ __('Name') }} <span class="text-danger">*</span></label>
        <ul class="nav nav-tabs" role="tablist">
            @foreach($locales as $locale)
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if($loop->first) active @endif" type="button"
                            data-bs-toggle="tab" data-bs-target="#name-tab-{{ $locale }}" role="tab">
                        {{ $localeLabels[$locale] ?? strtoupper($locale) }}
                    </button>
                </li>
            @endforeach
        </ul>
        <div class="tab-content border border-top-0 rounded-bottom p-3 bg-white">
            @foreach($locales as $locale)
                <div class="tab-pane fade @if($loop->first) show active @endif" id="name-tab-{{ $locale }}" role="tabpanel">
                    <input type="text" name="name_translations[{{ $locale }}]"
                           class="form-control @error('name_translations.'.$locale) is-invalid @enderror"
                           value="{{ $nameTranslations[$locale] ?? '' }}"
                           @if($locale === 'en') required @endif
                           dir="{{ in_array($locale, config('locales.rtl', [])) ? 'rtl' : 'ltr' }}">
                    @error('name_translations.'.$locale)<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            @endforeach
        </div>
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
                <option value="{{ $balance->value }}" @selected(old('normal_balance', $accountModel?->normal_balance?->value) === $balance->value)>{{ $balance->label() }}</option>
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
        <label class="form-label">{{ __('Description') }}</label>
        <ul class="nav nav-tabs" role="tablist">
            @foreach($locales as $locale)
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if($loop->first) active @endif" type="button"
                            data-bs-toggle="tab" data-bs-target="#description-tab-{{ $locale }}" role="tab">
                        {{ $localeLabels[$locale] ?? strtoupper($locale) }}
                    </button>
                </li>
            @endforeach
        </ul>
        <div class="tab-content border border-top-0 rounded-bottom p-3 bg-white">
            @foreach($locales as $locale)
                <div class="tab-pane fade @if($loop->first) show active @endif" id="description-tab-{{ $locale }}" role="tabpanel">
                    <textarea name="description_translations[{{ $locale }}]" rows="3"
                              class="form-control @error('description_translations.'.$locale) is-invalid @enderror"
                              dir="{{ in_array($locale, config('locales.rtl', [])) ? 'rtl' : 'ltr' }}">{{ $descriptionTranslations[$locale] ?? '' }}</textarea>
                    @error('description_translations.'.$locale)<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            @endforeach
        </div>
    </div>
</div>
