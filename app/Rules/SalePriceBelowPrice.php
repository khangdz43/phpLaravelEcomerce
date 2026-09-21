<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SalePriceBelowPrice implements ValidationRule
{
    public function __construct(private readonly int|float $price) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value !== null && (float) $value >= $this->price) {
            $fail('Giá khuyến mãi phải nhỏ hơn giá gốc.');
        }
    }
}
