<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Admin - Products', description: 'Quản lý sản phẩm và tồn kho')]
class AdminProductController extends Controller
{
    #[OA\Get(
        path: '/api/admin/products',
        summary: 'Danh sách sản phẩm dành cho quản trị',
        security: [['bearerAuth' => []]],
        tags: ['Admin - Products'],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'low_stock', in: 'query', description: 'Chỉ lấy sản phẩm sắp hết hàng', required: false, schema: new OA\Schema(type: 'boolean')),
            new OA\Parameter(name: 'keyword', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 15)),
        ],
        responses: [new OA\Response(response: 200, description: 'Danh sách sản phẩm phân trang')]
    )]
    public function index(Request $request): JsonResponse
    {
        $products = Product::query()
            ->with('category:id,name,slug')
            ->when($request->filled('status'), fn($query) => $query->where('status', $request->string('status')))
            ->when($request->boolean('low_stock'), fn($query) => $query->where('stock', '<=', 5))
            ->when($request->filled('keyword'), function ($query) use ($request): void {
                $keyword = $request->string('keyword')->toString();
                $query->where(fn($query) => $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('sku', 'like', "%{$keyword}%"));
            })
            ->orderBy('stock')
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->successResponse([
            'items' => ProductResource::collection($products->items()),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
            ],
        ]);
    }

    #[OA\Patch(
        path: '/api/admin/products/{product}/stock',
        summary: 'Cập nhật tồn kho sản phẩm',
        security: [['bearerAuth' => []]],
        tags: ['Admin - Products'],
        parameters: [new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(properties: [new OA\Property(property: 'stock', type: 'integer', minimum: 0, example: 25)])
        ),
        responses: [new OA\Response(response: 200, description: 'Cập nhật tồn kho thành công')]
    )]
    public function updateStock(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate(['stock' => ['required', 'integer', 'min:0']]);
        $product->update([
            'stock' => $validated['stock'],
            'status' => $validated['stock'] === 0 ? 'out_of_stock' : ($product->status === 'out_of_stock' ? 'published' : $product->status),
        ]);

        return $this->successResponse(new ProductResource($product->load('category:id,name,slug')), 'Cập nhật tồn kho thành công.');
    }
}
