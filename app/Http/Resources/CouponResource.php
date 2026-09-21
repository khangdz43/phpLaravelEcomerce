<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'code'                 => $this->code,
            'type'                 => $this->type,
            'value'                => $this->value,
            'minimum_order_amount' => $this->minimum_order_amount,
            'usage_limit'          => $this->usage_limit,
            'used_count'           => $this->used_count,
            'is_active'            => $this->is_active,
            'starts_at'            => $this->starts_at?->toIso8601String(),
            'expires_at'           => $this->expires_at?->toIso8601String(),
            'discount_label'       => $this->type === 'percent'
                ? "{$this->value}% OFF"
                : number_format($this->value) . 'đ OFF',
        ];
    }
}
