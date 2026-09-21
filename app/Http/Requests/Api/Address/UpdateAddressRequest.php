<?php

namespace App\Http\Requests\Api\Address;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipient_name' => ['sometimes', 'string', 'max:100'],
            'phone'          => ['sometimes', 'string', 'max:20'],
            'address_line'   => ['sometimes', 'string', 'max:255'],
            'ward'           => ['sometimes', 'nullable', 'string', 'max:100'],
            'district'       => ['sometimes', 'nullable', 'string', 'max:100'],
            'province'       => ['sometimes', 'string', 'max:100'],
            'is_default'     => ['sometimes', 'boolean'],
        ];
    }
}
