<?php

namespace App\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', 'unique:accounts,code,' . $this->account->id],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:asset,liability,equity,revenue,expense'],
            'parent_id' => ['nullable', 'exists:accounts,id', function ($attribute, $value, $fail) {
                // Prevent account from being its own parent
                if ($value && $value == $this->account->id) {
                    $fail('An account cannot be its own parent.');
                }
            }],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
