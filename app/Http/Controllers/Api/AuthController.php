<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[OA\Tag(name: "Authentication", description: "User Registration, Login, Logout, and Profile endpoints")]
class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    #[OA\Post(
        path: "/api/auth/register",
        summary: "Đăng ký tài khoản mới",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Nguyễn Văn A"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "password123")
                ]
            )
        ),
        tags: ["Authentication"],
        responses: [
            new OA\Response(response: 201, description: "Đăng ký thành công"),
            new OA\Response(response: 422, description: "Dữ liệu không hợp lệ")
        ]
    )]
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $this->authService->register($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Đăng ký tài khoản thành công.',
            'data' => $data,
        ], Response::HTTP_CREATED);
    }

    #[OA\Post(
        path: "/api/auth/login",
        summary: "Đăng nhập",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "admin@gmail.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password")
                ]
            )
        ),
        tags: ["Authentication"],
        responses: [
            new OA\Response(response: 200, description: "Đăng nhập thành công"),
            new OA\Response(response: 401, description: "Thông tin đăng nhập không chính xác")
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $this->authService->login($request->string('email')->toString(), $request->string('password')->toString());
        return $this->successResponse($data, 'Đăng nhập thành công.');
    }

    #[OA\Post(
        path: "/api/auth/logout",
        summary: "Đăng xuất",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Đăng xuất thành công"),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'status' => 'success',
            'message' => 'Đã đăng xuất thành công.',
        ]);
    }

    #[OA\Get(
        path: "/api/auth/me",
        summary: "Lấy thông tin tài khoản hiện tại",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Thông tin cá nhân"),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->authService->currentUser($request->user()),
        ]);
    }
}
