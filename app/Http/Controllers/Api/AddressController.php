<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Address\StoreAddressRequest;
use App\Http\Requests\Api\Address\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Services\AddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[OA\Tag(name: "Addresses", description: "Quản lý địa chỉ giao hàng")]
class AddressController extends Controller
{
    public function __construct(private readonly AddressService $addressService) {}

    #[OA\Get(
        path: "/api/addresses",
        summary: "Lấy danh sách địa chỉ của người dùng",
        security: [["bearerAuth" => []]],
        tags: ["Addresses"],
        responses: [
            new OA\Response(response: 200, description: "Danh sách địa chỉ")
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()->addresses()->latest()->get();
        return $this->successResponse(AddressResource::collection($addresses));
    }

    #[OA\Post(
        path: "/api/addresses",
        summary: "Tạo địa chỉ mới",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["full_name", "phone_number", "province", "district", "ward", "detail_address"],
                properties: [
                    new OA\Property(property: "full_name", type: "string", example: "Nguyễn Văn A"),
                    new OA\Property(property: "phone_number", type: "string", example: "0987654321"),
                    new OA\Property(property: "province", type: "string", example: "Hà Nội"),
                    new OA\Property(property: "district", type: "string", example: "Cầu Giấy"),
                    new OA\Property(property: "ward", type: "string", example: "Dịch Vọng"),
                    new OA\Property(property: "detail_address", type: "string", example: "Số 123 Xuân Thủy"),
                    new OA\Property(property: "is_default", type: "boolean", example: true)
                ]
            )
        ),
        tags: ["Addresses"],
        responses: [
            new OA\Response(response: 201, description: "Tạo địa chỉ thành công")
        ]
    )]
    public function store(StoreAddressRequest $request): JsonResponse
    {
        $address = $this->addressService->create($request->user(), $request->validated());

        return $this->successResponse(new AddressResource($address), 'Đã thêm địa chỉ mới.', Response::HTTP_CREATED);
    }

    #[OA\Put(
        path: "/api/addresses/{address}",
        summary: "Cập nhật địa chỉ",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "full_name", type: "string", example: "Nguyễn Văn A"),
                    new OA\Property(property: "phone_number", type: "string", example: "0987654321"),
                    new OA\Property(property: "province", type: "string", example: "Hà Nội"),
                    new OA\Property(property: "district", type: "string", example: "Cầu Giấy"),
                    new OA\Property(property: "ward", type: "string", example: "Dịch Vọng"),
                    new OA\Property(property: "detail_address", type: "string", example: "Số 456 Xuân Thủy"),
                    new OA\Property(property: "is_default", type: "boolean", example: false)
                ]
            )
        ),
        tags: ["Addresses"],
        parameters: [
            new OA\Parameter(name: "address", in: "path", description: "ID địa chỉ", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Cập nhật địa chỉ thành công")
        ]
    )]
    public function update(UpdateAddressRequest $request, Address $address): JsonResponse
    {
        abort_unless($address->user_id === $request->user()->id, 403);
        $updated = $this->addressService->update($address, $request->validated());

        return $this->successResponse(new AddressResource($updated), 'Cập nhật địa chỉ thành công.');
    }

    #[OA\Delete(
        path: "/api/addresses/{address}",
        summary: "Xóa địa chỉ",
        security: [["bearerAuth" => []]],
        tags: ["Addresses"],
        parameters: [
            new OA\Parameter(name: "address", in: "path", description: "ID địa chỉ", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Xóa thành công")
        ]
    )]
    public function destroy(Request $request, Address $address): JsonResponse
    {
        abort_unless($address->user_id === $request->user()->id, 403);
        $this->addressService->delete($address);

        return $this->successResponse(null, 'Đã xóa địa chỉ.');
    }

    #[OA\Post(
        path: "/api/addresses/{address}/set-default",
        summary: "Đặt địa chỉ mặc định",
        security: [["bearerAuth" => []]],
        tags: ["Addresses"],
        parameters: [
            new OA\Parameter(name: "address", in: "path", description: "ID địa chỉ", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Đặt mặc định thành công")
        ]
    )]
    public function setDefault(Request $request, Address $address): JsonResponse
    {
        abort_unless($address->user_id === $request->user()->id, 403);
        $updated = $this->addressService->setDefault($address);

        return $this->successResponse(new AddressResource($updated), 'Đặt địa chỉ mặc định thành công.');
    }
}
