<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[OA\Tag(name: "Categories", description: "Quản lý danh mục sản phẩm")]
class CategoryController extends Controller
{
    #[OA\Get(
        path: "/api/categories",
        summary: "Lấy danh sách danh mục sản phẩm",
        tags: ["Categories"],
        responses: [
            new OA\Response(response: 200, description: "Danh sách danh mục")
        ]
    )]
    public function index(): JsonResponse
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return $this->successResponse(CategoryResource::collection($categories));
    }

    #[OA\Get(
        path: "/api/categories/{category}",
        summary: "Chi tiết danh mục",
        tags: ["Categories"],
        parameters: [
            new OA\Parameter(name: "category", in: "path", description: "ID danh mục", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Chi tiết danh mục")
        ]
    )]
    public function show(Category $category): JsonResponse
    {
        return $this->successResponse(new CategoryResource($category->loadCount('products')));
    }

    #[OA\Post(
        path: "/api/categories",
        summary: "Tạo danh mục mới (Admin)",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Thời trang nam"),
                    new OA\Property(property: "description", type: "string", example: "Mô tả danh mục..."),
                    new OA\Property(property: "is_active", type: "boolean", example: true)
                ]
            )
        ),
        tags: ["Categories"],
        responses: [
            new OA\Response(response: 201, description: "Tạo danh mục thành công")
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $category = Category::create($validated);

        return $this->successResponse(new CategoryResource($category), 'Tạo danh mục thành công.', Response::HTTP_CREATED);
    }

    #[OA\Put(
        path: "/api/categories/{category}",
        summary: "Cập nhật danh mục (Admin)",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Thời trang nữ"),
                    new OA\Property(property: "description", type: "string", example: "Mô tả danh mục..."),
                    new OA\Property(property: "is_active", type: "boolean", example: true)
                ]
            )
        ),
        tags: ["Categories"],
        parameters: [
            new OA\Parameter(name: "category", in: "path", description: "ID danh mục", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Cập nhật thành công")
        ]
    )]
    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return $this->successResponse(new CategoryResource($category), 'Cập nhật danh mục thành công.');
    }

    #[OA\Delete(
        path: "/api/categories/{category}",
        summary: "Xóa danh mục (Admin)",
        security: [["bearerAuth" => []]],
        tags: ["Categories"],
        parameters: [
            new OA\Parameter(name: "category", in: "path", description: "ID danh mục", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Xóa thành công")
        ]
    )]
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return $this->successResponse(null, 'Đã xóa danh mục thành công.');
    }
}
