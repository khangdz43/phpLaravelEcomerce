<?php

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizedAccessException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    // request , trạm tiếp (next) , chuỗi permission 
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasPermission($permission)) {
            throw new UnauthorizedAccessException("Bạn thiếu quyền [{$permission}] để truy cập API này.");
        }

        return $next($request);
    }
}
