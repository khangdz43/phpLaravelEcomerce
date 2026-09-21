<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{

// chưa hiểu 
    public function toArray(Request $request): array
    {
        return [
            'product_id'   => $this->product_id,
            'product_name' => $this->whenLoaded('product', fn() => $this->product->name),
            'product_sku'  => $this->whenLoaded('product', fn() => $this->product->sku),
            'quantity'     => (int) $this->quantity,
            'price'        => (float) $this->price,
            'subtotal'     => (float) $this->subtotal,
        ];
    }
}