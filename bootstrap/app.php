<?php

use App\Exceptions\BusinessException;
use App\Exceptions\UnauthorizedAccessException;
use App\Http\Middleware\CheckPermission; // Import Middleware CheckPermission
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 1. ĐĂNG KÝ ALIAS MIDDLEWARE 
        $middleware->alias([
            'permission' => CheckPermission::class,
        ]);

        // 2. Cấu hình điều hướng cho Unauthenticated
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return null;
            }

            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontReport(BusinessException::class);

        $isApi = fn(Request $request) => $request->is('api/*') || $request->expectsJson();

        // 1. Lỗi Business Exception
        $exceptions->render(function (BusinessException $e, Request $request) use ($isApi) {
            if (! $isApi($request)) return null;

            return response()->json([
                'status'   => 'error',
                'code'     => $e->errorCode,
                'message'  => $e->getMessage(),
                'errors'   => $e->errors ?: null,
                'trace_id' => $request->header('X-Request-ID'),
            ], $e->statusCode);
        });

        // 2. Lỗi 401 Unauthorized (Chưa đăng nhập / Token sai)
        $exceptions->render(function (AuthenticationException $e, Request $request) use ($isApi) {
            if (! $isApi($request)) return null;

            return response()->json([
                'status'   => 'error',
                'code'     => 'UNAUTHENTICATED',
                'message'  => 'Yêu cầu xác thực token không hợp lệ hoặc đã hết hạn.',
                'errors'   => null,
                'trace_id' => $request->header('X-Request-ID'),
            ], Response::HTTP_UNAUTHORIZED);
        });

        // 3. Lỗi 403 Forbidden (Không có quyền truy cập - CheckPermission throw ra)
        $exceptions->render(function (UnauthorizedAccessException $e, Request $request) use ($isApi) {
            if (! $isApi($request)) return null;

            return response()->json([
                'status'   => 'error',
                'code'     => 'FORBIDDEN',
                'message'  => $e->getMessage() ?: 'Bạn không có quyền thực hiện thao tác này.',
                'errors'   => null,
                'trace_id' => $request->header('X-Request-ID'),
            ], Response::HTTP_FORBIDDEN);
        });

        // 4. Lỗi 404 Not Found
        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($isApi) {
            if (! $isApi($request)) return null;

            return response()->json([
                'status'  => 'error',
                'code'    => 'RESOURCE_NOT_FOUND',
                'message' => 'Resource hoặc URL không tồn tại.',
                'errors'  => null,
            ], Response::HTTP_NOT_FOUND);
        });

        // 5. Lỗi 422 Validation
        $exceptions->render(function (ValidationException $e, Request $request) use ($isApi) {
            if (! $isApi($request)) return null;

            return response()->json([
                'status'  => 'error',
                'code'    => 'VALIDATION_ERROR',
                'message' => 'Dữ liệu không hợp lệ.',
                'errors'  => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        // 6. Catch-all 500 Server Error
        $exceptions->render(function (Throwable $e, Request $request) use ($isApi) {
            if (! $isApi($request)) return null;

            return response()->json([
                'status'   => 'error',
                'code'     => 'INTERNAL_SERVER_ERROR',
                'message'  => 'Hệ thống đã xảy ra lỗi. Vui lòng thử lại sau!',
                'errors'   => config('app.debug') ? $e->getMessage() : null,
                'trace_id' => $request->header('X-Request-ID'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    })->create();
