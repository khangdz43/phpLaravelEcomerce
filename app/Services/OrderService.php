<?php

namespace App\Services;

use App\DTOs\Order\CreateOrderDTO;
use App\Enums\OrderStatus;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\ResourceNotFoundException;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepo,
        protected OrderRepositoryInterface $orderRepo
    ) {}

    public function createOrder(CreateOrderDTO $dto): Order
    {
        // áp dụng thằng tran saction
        return DB::transaction(function () use ($dto) {
            $totalAmount = 0;
            $orderItems = [];

            foreach ($dto->items as $item) {
                $product = $this->productRepo->findAndLock($item->productId);

                if (!$product) {
                    throw new ResourceNotFoundException("Không tồn tại sản phẩm");
                }

                if ($product->stock < $item->quantity) {
                    throw new InsufficientStockException($product->name);
                }

                $unitPrice = $product->sale_price ?? $product->price;
                $subtotal  = $unitPrice * $item->quantity;
                $totalAmount += $subtotal;

                $this->productRepo->decrementStock($product, $item->quantity);

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item->quantity,
                    'price'      => $unitPrice,
                    'subtotal'   => $subtotal,
                ];
            }

            $order = $this->orderRepo->create([
                'order_code'       => 'ORD-' . strtoupper(Str::random(8)),
                'customer_name'    => $dto->customerName,
                'customer_email'   => $dto->customerEmail,
                'customer_phone'   => $dto->customerPhone,
                'shipping_address' => $dto->shippingAddress,
                'total_amount'     => $totalAmount,
                'status'           => OrderStatus::PENDING->value,
            ]);

            $this->orderRepo->createItems($order, $orderItems);

            return $order;
        });
    }
}
