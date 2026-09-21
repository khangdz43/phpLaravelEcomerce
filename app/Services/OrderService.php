<?php

namespace App\Services;

use App\DTOs\Order\CreateOrderDTO;
use App\Enums\OrderStatus;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\ResourceNotFoundException;
use App\Models\Order;
use App\Models\User;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepo,
        protected OrderRepositoryInterface $orderRepo,
        protected CouponService $couponService,
        protected PaymentService $paymentService,
    ) {}

    public function createOrder(CreateOrderDTO $dto, ?User $user = null): Order
    {
        return DB::transaction(function () use ($dto, $user) {
            $totalAmount = 0;
            $orderItems = [];

            foreach ($dto->items as $item) {
                $product = $this->productRepo->findAndLock($item->productId);

                if (! $product || $product->status !== 'published') {
                    throw new ResourceNotFoundException('Không tồn tại sản phẩm');
                }

                if ($product->stock < $item->quantity) {
                    throw new InsufficientStockException($product->name);
                }

                $unitPrice = (int) ($product->sale_price ?? $product->price);
                $subtotal = $unitPrice * $item->quantity;
                $totalAmount += $subtotal;

                $this->productRepo->decrementStock($product, $item->quantity);

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'price' => $unitPrice,
                    'subtotal' => $subtotal,
                ];
            }

            $discount = 0;
            $coupon = null;

            if ($dto->couponCode) {
                $coupon = $this->couponService->findUsable($dto->couponCode);
                $discount = $this->couponService->discountFor($coupon, $totalAmount);
            }

            $payable = max(0, $totalAmount - $discount);

            $order = $this->orderRepo->create([
                'order_code' => 'ORD-'.strtoupper(Str::random(8)),
                'user_id' => $user?->id,
                'coupon_id' => $coupon?->id,
                'customer_name' => $dto->customerName,
                'customer_email' => $dto->customerEmail,
                'customer_phone' => $dto->customerPhone,
                'shipping_address' => $dto->shippingAddress,
                'subtotal_amount' => $totalAmount,
                'discount_amount' => $discount,
                'total_amount' => $payable,
                'status' => OrderStatus::PENDING->value,
                'payment_method' => $dto->paymentMethod,
                'notes' => $dto->notes,
            ]);

            $this->orderRepo->createItems($order, $orderItems);

            if ($coupon) {
                $coupon->increment('used_count');
                $coupon->redemptions()->create([
                    'order_id' => $order->id,
                    'user_id' => $user?->id,
                    'discount_amount' => $discount,
                ]);
            }

            $this->paymentService->createForOrder($order, $dto->paymentMethod);

            return $order->load(['items.product', 'payments', 'coupon']);
        });
    }

    public function updateStatus(Order $order, string $status): Order
    {
        return DB::transaction(function () use ($order, $status): Order {
            $previous = $order->status;

            if ($status === OrderStatus::CANCELLED->value && $previous !== OrderStatus::CANCELLED->value) {
                $this->restoreStock($order);
            }

            if ($status === OrderStatus::COMPLETED->value) {
                $this->paymentService->markPaid($order);
            }

            $order->update(['status' => $status]);

            return $order->refresh();
        });
    }

    public function cancel(Order $order, User $actor): Order
    {
        $isStaff = $actor->hasPermission('orders.update');
        abort_unless($isStaff || $order->user_id === $actor->id, 404);
        abort_unless(in_array($order->status, [OrderStatus::PENDING->value, OrderStatus::PROCESSING->value], true), 422);

        return $this->updateStatus($order, OrderStatus::CANCELLED->value);
    }

    private function restoreStock(Order $order): void
    {
        $order->loadMissing('items');

        foreach ($order->items as $item) {
            $product = $this->productRepo->findAndLock($item->product_id);

            if ($product) {
                $this->productRepo->incrementStock($product, (int) $item->quantity);
            }
        }
    }
}
