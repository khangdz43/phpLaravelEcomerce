<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Comment\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Product $product): JsonResponse
    {
        $comment = $product->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->validated('body'),
        ])->load('user:id,name');

        return response()->json([
            'status' => 'success',
            'data' => $comment,
        ], Response::HTTP_CREATED);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        abort_unless($comment->user_id === request()->user()->id, Response::HTTP_FORBIDDEN);

        $comment->delete();

        return response()->json(['status' => 'success']);
    }
}
