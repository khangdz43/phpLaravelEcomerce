<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\ProductSearchRequest;
use App\Http\Requests\Api\Product\StoreProductRequest;
use App\Http\Requests\Api\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[OA\Tag(name: "Products", description: "Product Catalog and Admin Management")]
class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    #[OA\Get(
        path: "/api/products",
        summary: "Danh sách sản phẩm (Có tìm kiếm & lọc)",
        tags: ["Products"],
        parameters: [
            new OA\Parameter(name: "keyword", in: "query", description: "Từ khóa tìm kiếm theo tên/mô tả", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "category_id", in: "query", description: "ID danh mục", required: false, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "min_price", in: "query", description: "Giá tối thiểu", required: false, schema: new OA\Schema(type: "number")),
            new OA\Parameter(name: "max_price", in: "query", description: "Giá tối đa", required: false, schema: new OA\Schema(type: "number")),
            new OA\Parameter(name: "sort_by", in: "query", description: "Trường sắp xếp (price, created_at, name)", required: false, schema: new OA\Schema(type: "string", default: "created_at")),
            new OA\Parameter(name: "sort_order", in: "query", description: "Hướng sắp xếp (asc, desc)", required: false, schema: new OA\Schema(type: "string", default: "desc")),
            new OA\Parameter(name: "per_page", in: "query", description: "Số phần tử trên 1 trang", required: false, schema: new OA\Schema(type: "integer", default: 15))
        ],
        responses: [
            new OA\Response(response: 200, description: "Danh sách sản phẩm phân trang")
        ]
    )]
    public function index(ProductSearchRequest $request): JsonResponse
    {
        $products = $this->productService
            ->searchPublished($request->validated());

        return $this->successResponse([
            'items' => ProductResource::collection($products->items()),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
                'has_more_pages' => $products->hasMorePages(),
            ],
        ]);
    }

    #[OA\Get(
        path: "/api/products/{product}",
        summary: "Chi tiết sản phẩm",
        tags: ["Products"],
        parameters: [
            new OA\Parameter(name: "product", in: "path", description: "ID hoặc slug sản phẩm", required: true, schema: new OA\Schema(type: "string", example: "iphone-15-pro-max-256gb"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Chi tiết sản phẩm"),
            new OA\Response(response: 404, description: "Sản phẩm không tồn tại")
        ]
    )]
    public function show(string $product): JsonResponse
    {
        $product = Product::query()
            ->where('slug', $product)
            ->orWhere(fn($query) => ctype_digit($product) ? $query->whereKey((int) $product) : $query->whereRaw('1 = 0'))
            ->firstOrFail();

        return $this->successResponse(new ProductResource($product->load('category:id,name,slug')));
    }

    #[OA\Post(
        path: "/api/products",
        summary: "Tạo mới sản phẩm (Admin/Staff)",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: [
                    "category_id",
                    "name",
                    "sku",
                    "price",
                    "stock",
                    "description",
                    "status"
                ],
                properties: [
                    new OA\Property(
                        property: "category_id",
                        type: "integer",
                        example: 1
                    ),
                    new OA\Property(
                        property: "name",
                        type: "string",
                        example: "Áo Polo Nam Premium Cotton"
                    ),
                    new OA\Property(
                        property: "sku",
                        type: "string",
                        example: "POLO-NAM-99"
                    ),
                    new OA\Property(
                        property: "price",
                        type: "number",
                        format: "float",
                        example: 350000
                    ),
                    new OA\Property(
                        property: "sale_price",
                        type: "number",
                        format: "float",
                        example: 299000
                    ),
                    new OA\Property(
                        property: "stock",
                        type: "integer",
                        example: 50
                    ),
                    new OA\Property(
                        property: "description",
                        type: "string",
                        example: "Áo polo chất liệu cotton co dãn 4 chiều, thoáng mát."
                    ),
                    new OA\Property(
                        property: "status",
                        type: "string",
                        example: "published"
                    )
                ]
            )
        ),
        tags: ["Products"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Tạo sản phẩm thành công"
            ),
            new OA\Response(
                response: 403,
                description: "Không có quyền thực hiện"
            )
        ]
    )]

    public function store(StoreProductRequest $request): JsonResponse
    {
        return $this->successResponse(
            $this->productService->create($request->validated()),
            'Tạo sản phẩm thành công!',
            Response::HTTP_CREATED
        );
    }

    #[OA\Put(
        path: "/api/products/{product}",
        summary: "Cập nhật sản phẩm (Admin/Staff)",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Sản phẩm đã sửa"),
                    new OA\Property(property: "price", type: "number", example: 199000)
                ]
            )
        ),
        tags: ["Products"],
        parameters: [
            new OA\Parameter(name: "product", in: "path", description: "ID sản phẩm", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Cập nhật thành công"),
            new OA\Response(response: 403, description: "Không có quyền")
        ]
    )]
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        Gate::authorize('update', $product);

        return $this->successResponse(
            $this->productService->update($product, $request->validated()),
            'Cập nhật sản phẩm thành công!'
        );
    }

    #[OA\Delete(
        path: "/api/products/{product}",
        summary: "Xóa sản phẩm (Admin/Staff)",
        security: [["bearerAuth" => []]],
        tags: ["Products"],
        parameters: [
            new OA\Parameter(name: "product", in: "path", description: "ID sản phẩm", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Xóa thành công"),
            new OA\Response(response: 403, description: "Không có quyền")
        ]
    )]
    public function destroy(Product $product): JsonResponse
    {
        Gate::authorize('delete', $product);
        $this->productService->deleteById($product->id);

        return $this->successResponse(null, 'Xóa sản phẩm thành công!');
    }
}
