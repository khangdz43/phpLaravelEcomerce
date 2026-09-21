<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'order_code'       => $this->order_code,
            'customer' => [
                'name'    => $this->customer_name,
                'email'   => $this->customer_email,
                'phone'   => $this->customer_phone,
                'address' => $this->shipping_address,
            ],
            'total_amount'     => (float) $this->total_amount,
            'status'           => $this->status,
            'created_at'       => $this->created_at->toIso8601String(),
            'items'            => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
