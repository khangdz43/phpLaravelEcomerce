<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CouponResource;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Coupons", description: "Mã giảm giá và khuyến mãi")]
class CouponController extends Controller
{
    public function __construct(private readonly CouponService $couponService) {}

    #[OA\Post(
        path: "/api/coupons/validate",
        summary: "Kiểm tra mã giảm giá",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["code", "order_amount"],
                properties: [
                    new OA\Property(property: "code", type: "string", example: "WELCOME10"),
                    new OA\Property(property: "order_amount", type: "number", example: 500000)
                ]
            )
        ),
        tags: ["Coupons"],
        responses: [
            new OA\Response(response: 200, description: "Mã giảm giá hợp lệ"),
            new OA\Response(response: 422, description: "Mã giảm giá không hợp lệ hoặc không đủ điều kiện")
        ]
    )]
    public function validateCoupon(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
            'order_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $subtotal = (int) $validated['order_amount'];
        $coupon = $this->couponService->findUsable($validated['code']);
        $discount = $this->couponService->discountFor($coupon, $subtotal);

        return $this->successResponse([
            'coupon' => new CouponResource($coupon),
            'discount_amount' => $discount,
            'final_total' => max(0, $subtotal - $discount),
        ], 'Mã giảm giá hợp lệ.');
    }
}
