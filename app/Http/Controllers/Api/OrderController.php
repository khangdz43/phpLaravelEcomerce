<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Order\CreateOrderDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Order\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[OA\Tag(name: "Orders", description: "Customer Order Placement and Order History")]
class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    #[OA\Post(
        path: "/api/orders",
        summary: "Tạo đơn hàng mới (Checkout)",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: [
                    "customer_name",
                    "customer_email",
                    "customer_phone",
                    "shipping_address",
                    "items"
                ],
                properties: [
                    new OA\Property(property: "customer_name", type: "string", example: "Nguyễn Văn A"),
                    new OA\Property(property: "customer_email", type: "string", format: "email", example: "customer@example.com"),
                    new OA\Property(property: "customer_phone", type: "string", example: "0987654321"),
                    new OA\Property(property: "shipping_address", type: "string", example: "123 Nguyễn Huệ, Q.1, TP.HCM"),
                    new OA\Property(
                        property: "items",
                        type: "array",
                        minItems: 1,
                        items: new OA\Items(
                            type: "object",
                            required: ["product_id", "quantity"],
                            properties: [
                                new OA\Property(property: "product_id", type: "integer", example: 1),
                                new OA\Property(property: "quantity", type: "integer", minimum: 1, example: 2)
                            ]
                        ),
                        example: [["product_id" => 1, "quantity" => 2]]
                    ),
                    new OA\Property(
                        property: "payment_method",
                        type: "string",
                        enum: ["cod", "bank_transfer"],
                        example: "cod",
                        nullable: true
                    ),
                    new OA\Property(property: "coupon_code", type: "string", example: "SUMMER2026", nullable: true),
                    new OA\Property(property: "notes", type: "string", example: "Giao trong giờ hành chính", nullable: true)
                ]
            )
        ),
        tags: ["Orders"],
        responses: [
            new OA\Response(response: 201, description: "Tạo đơn hàng thành công"),
            new OA\Response(response: 422, description: "Dữ liệu đơn hàng không hợp lệ")
        ]
    )]



    public function store(StoreOrderRequest $request): JsonResponse
    {
        $dto = CreateOrderDTO::fromRequest($request);
        $order = $this->orderService->createOrder($dto, $request->user());

        return $this->successResponse(
            new OrderResource($order->load('items.product')),
            'Order created successfully',
            Response::HTTP_CREATED
        );
    }

    #[OA\Get(
        path: "/api/orders",
        summary: "Danh sách đơn hàng của tôi",
        security: [["bearerAuth" => []]],
        tags: ["Orders"],
        responses: [
            new OA\Response(response: 200, description: "Danh sách đơn hàng phân trang")
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()->orders()
            ->with('items.product')
            ->latest()
            ->paginate(10);

        return $this->successResponse([
            'items' => OrderResource::collection($orders->items()),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    #[OA\Get(
        path: "/api/orders/{order}",
        summary: "Xem chi tiết đơn hàng",
        security: [["bearerAuth" => []]],
        tags: ["Orders"],
        parameters: [
            new OA\Parameter(name: "order", in: "path", description: "ID đơn hàng", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Chi tiết đơn hàng"),
            new OA\Response(response: 404, description: "Đơn hàng không tồn tại hoặc không thuộc về người dùng")
        ]
    )]
    public function show(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id || $request->user()->hasPermission('orders.view'), 404);

        return $this->successResponse(new OrderResource($order->load('items.product', 'payments', 'coupon')));
    }

    #[OA\Post(
        path: "/api/orders/{order}/cancel",
        summary: "Hủy đơn hàng",
        security: [["bearerAuth" => []]],
        tags: ["Orders"],
        parameters: [
            new OA\Parameter(name: "order", in: "path", description: "ID đơn hàng", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Hủy thành công"),
            new OA\Response(response: 400, description: "Đơn hàng không thể hủy ở trạng thái hiện tại")
        ]
    )]
    public function cancel(Request $request, Order $order): JsonResponse
    {
        $order = $this->orderService->cancel($order, $request->user());

        return $this->successResponse(new OrderResource($order->load('items.product')), 'Đã hủy đơn hàng.');
    }
}
