<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Admin - Orders", description: "Quản lý đơn hàng dành cho Admin & Nhân viên")]
class AdminOrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    #[OA\Get(
        path: "/api/admin/orders",
        summary: "Quản trị: Danh sách tất cả đơn hàng",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Orders"],
        parameters: [
            new OA\Parameter(name: "status", in: "query", description: "Lọc theo trạng thái", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "per_page", in: "query", description: "Số đơn hàng mỗi trang", required: false, schema: new OA\Schema(type: "integer", default: 15))
        ],
        responses: [
            new OA\Response(response: 200, description: "Danh sách đơn hàng toàn hệ thống"),
            new OA\Response(response: 403, description: "Không có quyền quản trị")
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $status = $request->string('status')->toString();
        $query = Order::query()->with(['user:id,name,email', 'items.product'])->latest();

        if (! empty($status)) {
            $query->where('status', $status);
        }

        $orders = $query->paginate($request->integer('per_page', 15));

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
        path: "/api/admin/orders/{order}",
        summary: "Quản trị: Chi tiết đơn hàng",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Orders"],
        parameters: [
            new OA\Parameter(name: "order", in: "path", description: "ID đơn hàng", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Chi tiết đơn hàng")
        ]
    )]
    public function show(Order $order): JsonResponse
    {
        return $this->successResponse(new OrderResource($order->load('user', 'items.product', 'payments', 'coupon')));
    }

    #[OA\Put(
        path: "/api/admin/orders/{order}/status",
        summary: "Quản trị: Cập nhật trạng thái đơn hàng",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["status"],
                properties: [
                    new OA\Property(property: "status", type: "string", example: "completed")
                ]
            )
        ),
        tags: ["Admin - Orders"],
        parameters: [
            new OA\Parameter(name: "order", in: "path", description: "ID đơn hàng", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Cập nhật trạng thái thành công"),
            new OA\Response(response: 422, description: "Trạng thái không hợp lệ")
        ]
    )]
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        $updatedOrder = $this->orderService->updateStatus($order, $request->string('status')->toString(), $request->user());

        return $this->successResponse(new OrderResource($updatedOrder), 'Cập nhật trạng thái đơn hàng thành công.');
    }
}
