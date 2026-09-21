<?php

namespace App\Http\Controllers\Web;

use App\DTOs\Order\CreateOrderDTO;
use App\DTOs\Order\CreateOrderItemDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\CheckoutRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly CartService $cartService,
        private readonly CouponService $couponService,
    ) {}

    public function create(Request $request): View
    {
        $cart = $this->cartService->current($request->user(), $request->session()->getId());

        return view('shop.checkout', [
            'cartPayload' => $this->cartService->toClient($cart),
            'addresses' => $request->user()?->addresses()->latest()->get() ?? collect(),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $items = $this->resolveItems($request);

        if ($items === []) {
            throw ValidationException::withMessages([
                'items' => 'Giỏ hàng đang trống.',
            ]);
        }

        $dto = new CreateOrderDTO(
            customerName: $request->validated('customer_name'),
            customerEmail: $request->validated('customer_email'),
            customerPhone: $request->validated('customer_phone'),
            shippingAddress: $request->validated('shipping_address'),
            items: array_map(fn(array $item): CreateOrderItemDTO => new CreateOrderItemDTO(
                productId: (int) $item['product_id'],
                quantity: (int) $item['quantity'],
            ), $items),
            couponCode: $request->validated('coupon_code') ?: null,
            paymentMethod: $request->validated('payment_method') ?? 'cod',
            notes: $request->validated('notes') ?: null,
        );

        $order = $this->orderService->createOrder($dto, $request->user());

        $cart = $this->cartService->current($request->user(), $request->session()->getId());
        $this->cartService->clear($cart);

        return redirect()->route('shop.checkout.success', $order->order_code);
    }

    public function previewCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
            'subtotal' => ['required', 'integer', 'min:0'],
        ]);

        return response()->json($this->couponService->preview($validated['code'], (int) $validated['subtotal'], $request->user()));
    }

    public function success(Order $order): View
    {
        return view('shop.success', [
            'order' => $order->load(['items.product', 'payments', 'coupon']),
        ]);
    }

    /**
     * @return array<int, array{product_id: int, quantity: int}>
     */
    private function resolveItems(CheckoutRequest $request): array
    {
        $raw = json_decode((string) $request->validated('items'), true);

        if (is_array($raw) && $raw !== []) {
            return array_values(array_filter($raw, fn(array $item): bool => ($item['product_id'] ?? 0) > 0 && ($item['quantity'] ?? 0) > 0));
        }

        $cart = $this->cartService->current($request->user(), $request->session()->getId());

        return $this->cartService->checkoutItems($cart);
    }
}
