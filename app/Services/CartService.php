<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function current(?User $user, string $sessionId): Cart
    {
        if ($user) {
            return Cart::query()
                ->firstOrCreate(
                    ['user_id' => $user->id, 'status' => 'active'],
                    ['session_id' => $sessionId],
                )
                ->load(['items.product.category']);
        }

        return Cart::query()
            ->firstOrCreate(
                ['session_id' => $sessionId, 'status' => 'active', 'user_id' => null],
            )
            ->load(['items.product.category']);
    }

    public function add(Cart $cart, Product $product, int $quantity = 1): Cart
    {
        abort_unless($product->status === 'published', 404);

        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        $item->quantity = ($item->exists ? $item->quantity : 0) + $quantity;
        $item->unit_price = (int) ($product->sale_price ?? $product->price);
        $item->save();

        return $cart->refresh()->load(['items.product.category']);
    }

    public function updateQuantity(Cart $cart, int $productId, int $quantity): Cart
    {
        $item = $cart->items()->where('product_id', $productId)->firstOrFail();

        if ($quantity < 1) {
            $item->delete();
        } else {
            $item->update(['quantity' => $quantity]);
        }

        return $cart->refresh()->load(['items.product.category']);
    }

    public function remove(Cart $cart, int $productId): Cart
    {
        $cart->items()->where('product_id', $productId)->delete();

        return $cart->refresh()->load(['items.product.category']);
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
        $cart->update(['status' => 'converted']);
    }

    public function mergeGuestCart(User $user, string $sessionId): void
    {
        DB::transaction(function () use ($user, $sessionId): void {
            $guestCart = Cart::query()
                ->where('session_id', $sessionId)
                ->whereNull('user_id')
                ->where('status', 'active')
                ->with('items.product')
                ->first();

            if (! $guestCart || $guestCart->items->isEmpty()) {
                return;
            }

            $userCart = $this->current($user, $sessionId);

            foreach ($guestCart->items as $item) {
                $this->add($userCart, $item->product ?? Product::findOrFail($item->product_id), $item->quantity);
            }

            $guestCart->items()->delete();
            $guestCart->update(['status' => 'merged']);
        });
    }

    public function checkoutItems(Cart $cart): array
    {
        return $cart->items->map(fn(CartItem $item): array => [
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
        ])->all();
    }

    public function toClient(Cart $cart): array
    {
        $items = $cart->items->map(function (CartItem $item): array {
            $product = $item->product;

            return [
                'id' => $item->product_id,
                'name' => $product?->name ?? 'Sản phẩm',
                'price' => (int) ($item->unit_price ?: ($product?->sale_price ?? $product?->price ?? 0)),
                'quantity' => $item->quantity,
                'slug' => $product?->slug,
            ];
        });

        return [
            'items' => $items->values(),
            'count' => $items->sum('quantity'),
            'total' => $items->sum(fn(array $item): int => $item['price'] * $item['quantity']),
        ];
    }
}
