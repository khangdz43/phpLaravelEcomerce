<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Comment\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[OA\Tag(name: "Comments", description: "Đánh giá & Bình luận sản phẩm")]
class CommentController extends Controller
{
    #[OA\Get(
        path: "/api/products/{product}/comments",
        summary: "Lấy danh sách bình luận/đánh giá của sản phẩm",
        tags: ["Comments"],
        parameters: [
            new OA\Parameter(name: "product", in: "path", description: "ID sản phẩm", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Danh sách đánh giá")
        ]
    )]
    public function index(Product $product): JsonResponse
    {
        $comments = $product->comments()->with('user:id,name')->latest()->paginate(10);

        return $this->successResponse([
            'items' => CommentResource::collection($comments->items()),
            'pagination' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'total' => $comments->total(),
            ],
        ]);
    }

    #[OA\Post(
        path: "/api/products/{product}/comments",
        summary: "Viết đánh giá/bình luận sản phẩm",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["content", "rating"],
                properties: [
                    new OA\Property(property: "content", type: "string", example: "Sản phẩm rất tốt! Giao hàng nhanh."),
                    new OA\Property(property: "rating", type: "integer", example: 5)
                ]
            )
        ),
        tags: ["Comments"],
        parameters: [
            new OA\Parameter(name: "product", in: "path", description: "ID sản phẩm", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 201, description: "Đánh giá thành công")
        ]
    )]
    public function store(StoreCommentRequest $request, Product $product): JsonResponse
    {
        $body = $request->input('body', $request->input('content'));

        $comment = $product->comments()->create([
            'user_id' => $request->user()->id,
            'body' => (string) $body,
        ]);

        return $this->successResponse(new CommentResource($comment->load('user')), 'Gửi đánh giá thành công.', Response::HTTP_CREATED);
    }

    #[OA\Delete(
        path: "/api/comments/{comment}",
        summary: "Xóa bình luận",
        security: [["bearerAuth" => []]],
        tags: ["Comments"],
        parameters: [
            new OA\Parameter(name: "comment", in: "path", description: "ID bình luận", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Xóa thành công")
        ]
    )]
    public function destroy(Request $request, Comment $comment): JsonResponse
    {
        abort_unless($comment->user_id === $request->user()->id || $request->user()->hasPermission('products.delete'), 403);
        $comment->delete();

        return $this->successResponse(null, 'Đã xóa đánh giá.');
    }
}
