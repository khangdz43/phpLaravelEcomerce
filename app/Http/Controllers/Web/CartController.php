<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Cart\CartItemRequest;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService) {}

    public function show(Request $request): JsonResponse
    {
        return response()->json($this->payload($request));
    }

    public function store(CartItemRequest $request): JsonResponse
    {
        $cart = $this->cartService->current($request->user(), $request->session()->getId());
        $product = Product::query()->findOrFail($request->integer('product_id'));
        $this->cartService->add($cart, $product, $request->integer('quantity'));

        return response()->json($this->payload($request));
    }

    public function update(Request $request, int $productId): JsonResponse
    {
        $validated = $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:99']]);
        $cart = $this->cartService->current($request->user(), $request->session()->getId());
        $this->cartService->updateQuantity($cart, $productId, (int) $validated['quantity']);

        return response()->json($this->payload($request));
    }

    public function destroy(Request $request, int $productId): JsonResponse
    {
        $cart = $this->cartService->current($request->user(), $request->session()->getId());
        $this->cartService->remove($cart, $productId);

        return response()->json($this->payload($request));
    }

    private function payload(Request $request): array
    {
        $cart = $this->cartService->current($request->user(), $request->session()->getId());

        return $this->cartService->toClient($cart);
    }
}
