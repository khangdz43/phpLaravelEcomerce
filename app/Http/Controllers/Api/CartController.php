<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Cart\CartItemRequest;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[OA\Tag(name: "Cart", description: "Shopping Cart Management")]
class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService) {}

    #[OA\Get(
        path: "/api/cart",
        summary: "Xem giỏ hàng hiện tại",
        security: [["bearerAuth" => []]],
        tags: ["Cart"],
        responses: [
            new OA\Response(response: 200, description: "Nội dung giỏ hàng")
        ]
    )]
    public function show(Request $request): JsonResponse
    {
        $cart = $this->cartService->current($request->user(), $this->sessionKey($request));

        return $this->successResponse($this->cartService->toClient($cart));
    }

    #[OA\Post(
        path: "/api/cart",
        summary: "Thêm sản phẩm vào giỏ hàng",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["product_id", "quantity"],
                properties: [
                    new OA\Property(property: "product_id", type: "integer", example: 1),
                    new OA\Property(property: "quantity", type: "integer", example: 2)
                ]
            )
        ),
        tags: ["Cart"],
        responses: [
            new OA\Response(response: 201, description: "Đã thêm vào giỏ hàng")
        ]
    )]
    public function store(CartItemRequest $request): JsonResponse
    {
        $cart = $this->cartService->current($request->user(), $this->sessionKey($request));
        $product = Product::query()->findOrFail($request->integer('product_id'));
        $this->cartService->add($cart, $product, $request->integer('quantity'));

        return $this->successResponse($this->cartService->toClient($cart), 'Đã thêm vào giỏ hàng.', Response::HTTP_CREATED);
    }

    #[OA\Put(
        path: "/api/cart/{productId}",
        summary: "Cập nhật số lượng sản phẩm trong giỏ hàng",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["quantity"],
                properties: [
                    new OA\Property(property: "quantity", type: "integer", example: 5)
                ]
            )
        ),
        tags: ["Cart"],
        parameters: [
            new OA\Parameter(name: "productId", in: "path", description: "ID sản phẩm", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Cập nhật giỏ hàng thành công")
        ]
    )]
    public function update(Request $request, int $productId): JsonResponse
    {
        $validated = $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:99']]);
        $cart = $this->cartService->current($request->user(), $this->sessionKey($request));
        $this->cartService->updateQuantity($cart, $productId, (int) $validated['quantity']);

        return $this->successResponse($this->cartService->toClient($cart));
    }

    #[OA\Delete(
        path: "/api/cart/{productId}",
        summary: "Xóa sản phẩm khỏi giỏ hàng",
        security: [["bearerAuth" => []]],
        tags: ["Cart"],
        parameters: [
            new OA\Parameter(name: "productId", in: "path", description: "ID sản phẩm", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Xóa thành công")
        ]
    )]
    public function destroy(Request $request, int $productId): JsonResponse
    {
        $cart = $this->cartService->current($request->user(), $this->sessionKey($request));
        $this->cartService->remove($cart, $productId);

        return $this->successResponse($this->cartService->toClient($cart));
    }

    private function sessionKey(Request $request): string
    {
        return 'api-'.$request->user()->id;
    }
}
