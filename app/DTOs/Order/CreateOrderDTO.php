<?php

namespace App\DTOs\Order;

use App\Http\Requests\Api\Order\StoreOrderRequest;

readonly class CreateOrderDTO
{
    /** gợi í PHP doc 
     * @param CreateOrderItemDTO[] $items
     */
    public function __construct(
        public string $customerName,
        public string $customerEmail,
        public string $customerPhone,
        public string $shippingAddress,
        public array $items
    ) {}

    public static function fromRequest(StoreOrderRequest $request): self
    {
        $validated = $request->validated();

        $items = array_map(
            fn(array $item) => new CreateOrderItemDTO(
                productId: (int) $item['product_id'],
                quantity: (int) $item['quantity']
            ),
            $validated['items']
        );

        return new self(
            customerName: $validated['customer_name'],
            customerEmail: $validated['customer_email'],
            customerPhone: $validated['customer_phone'],
            shippingAddress: $validated['shipping_address'],
            items: $items
        );
    }
}
