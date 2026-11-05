<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'phone' => normalize_phone($this->input('phone')),
            'address' => trim((string) $this->input('address')),
            'note' => trim((string) $this->input('note')) ?: null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => ['required', 'regex:/^01[3-9]\d{8}$/'],
            'email' => ['nullable', 'email', 'max:190'],
            'address' => ['required', 'string', 'min:10', 'max:500'],
            'district' => ['required', Rule::in(config('store.districts'))],
            'area' => ['required', Rule::in(['inside', 'outside'])],
            'note' => ['nullable', 'string', 'max:500'],
            'save_address' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.*' => 'Please enter your full name.',
            'phone.*' => 'Enter a valid Bangladeshi mobile number (e.g. 01712345678).',
            'address.*' => 'Please enter your full address (house, road, area).',
            'district.*' => 'Please select your district.',
            'email.email' => 'Enter a valid email address.',
        ];
    }
}
