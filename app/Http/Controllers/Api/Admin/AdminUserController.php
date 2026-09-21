<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Admin - Users', description: 'Quản lý tài khoản người dùng')]
class AdminUserController extends Controller
{
    #[OA\Get(
        path: '/api/admin/users',
        summary: 'Danh sách người dùng dành cho quản trị',
        security: [['bearerAuth' => []]],
        tags: ['Admin - Users'],
        parameters: [
            new OA\Parameter(name: 'keyword', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'role', in: 'query', required: false, schema: new OA\Schema(type: 'string', example: 'customer')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 15)),
        ],
        responses: [new OA\Response(response: 200, description: 'Danh sách người dùng phân trang')]
    )]
    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->with('roles:id,name')
            ->withCount('orders')
            ->when($request->filled('role'), fn($query) => $query->whereHas('roles', fn($roles) => $roles->where('name', $request->string('role'))))
            ->when($request->filled('keyword'), function ($query) use ($request): void {
                $keyword = $request->string('keyword')->toString();
                $query->where(fn($query) => $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%"));
            })
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->successResponse([
            'items' => UserResource::collection($users->items()),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }

    #[OA\Get(
        path: '/api/admin/users/{user}',
        summary: 'Chi tiết người dùng và lịch sử mua hàng',
        security: [['bearerAuth' => []]],
        tags: ['Admin - Users'],
        parameters: [new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Chi tiết người dùng')]
    )]
    public function show(User $user): JsonResponse
    {
        $user->load('roles:id,name')->loadCount(['orders', 'addresses']);

        return $this->successResponse(new UserResource($user));
    }
}
