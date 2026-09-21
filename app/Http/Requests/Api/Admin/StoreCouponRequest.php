<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code'                 => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'type'                 => ['required', 'in:percent,fixed'],
            'value'                => ['required', 'integer', 'min:1'],
            'minimum_order_amount' => ['nullable', 'integer', 'min:0'],
            'usage_limit'          => ['nullable', 'integer', 'min:1'],
            'is_active'            => ['boolean'],
            'starts_at'            => ['nullable', 'date'],
            'expires_at'           => ['nullable', 'date', 'after:starts_at'],
        ];
    }
}
