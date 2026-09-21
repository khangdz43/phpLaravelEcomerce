<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Order\CreateOrderDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Order\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{

    public function __construct(
        protected OrderService $orderService
    ) {}

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $dto = CreateOrderDTO::fromRequest($request);
        $order = $this->orderService->createOrder($dto);

        return $this->successResponse(
            new OrderResource($order->load('items.product')),
            'Order created successfully',
            Response::HTTP_CREATED
        );
    }
}
