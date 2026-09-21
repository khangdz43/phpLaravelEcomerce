<?php

namespace App\DTOs\Order;

readonly class CreateOrderItemDTO
{
    public function __construct(
        public int $productId,
        public int $quantity
    ) {}
}
