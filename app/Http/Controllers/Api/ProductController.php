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
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{

    public function __construct(
        protected ProductService $productService
    ) {}

    public function index(ProductSearchRequest $request): JsonResponse
    {
        $products = $this->productService
            ->searchPublished($request->validated());

        return $this->successResponse([
            // duyệt qua từng phần tử và chạy qua  ProductResource
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

    public function show(Product $product): JsonResponse
    {
        return $this->successResponse(new ProductResource($product->load('category:id,name,slug')));
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        return $this->successResponse(
            $this->productService->create($request->validated()),
            'Tạo sản phẩm thành công!',
            Response::HTTP_CREATED
        );
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        Gate::authorize('update', $product);

        return $this->successResponse(
            $this->productService->update($product, $request->validated()),
            'Cập nhật sản phẩm thành công!'
        );
    }

    public function destroy(Product $product): JsonResponse
    {
        Gate::authorize('delete', $product);
        $this->productService->deleteById($product->id);

        return $this->successResponse(null, 'Xóa sản phẩm thành công!');
    }
}
