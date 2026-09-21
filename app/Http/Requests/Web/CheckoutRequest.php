<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'shipping_address' => ['required', 'string', 'max:1000'],
            'items' => ['nullable', 'json'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'payment_method' => ['nullable', 'in:cod,bank_transfer'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $items = json_decode((string) $this->input('items'), true);

        if (! is_array($items)) {
            return;
        }

        $this->merge([
            'items' => json_encode(array_map(fn(array $item): array => [
                'product_id' => (int) ($item['id'] ?? $item['product_id'] ?? 0),
                'quantity' => (int) ($item['quantity'] ?? 0),
            ], $items)),
        ]);
    }
}
