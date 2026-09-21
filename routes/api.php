<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

// xem products list
Route::get('/products', [ProductController::class, 'index']);

// path variable
Route::get('/products/{product}', [ProductController::class, 'show']);

// create product
// Route::post('/products', [ProductController::class, 'store']);

// sửa xóa
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/products/{product}', [ProductController::class, 'update'])
        ->middleware('permission:products.update');
});
// Route::delete('/products/{id}', [ProductController::class, 'destroy']);

// orrder
Route::post('/orders', [OrderController::class, 'store']);


// Authentication public endpoints
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);


// nhóm API cần xác thực mới được
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
});

// xác thực phân quyền
Route::middleware('auth:sanctum')->group(function () {
    // nếu pass xác thực login rồi 
    // xem user đó là ai có quyền trong 

    // Chỉ User có quyền 'products.create' mới tạo được sản phẩm
    Route::post('/products', [ProductController::class, 'store'])
        // vào middleware vào file checkPermission để chạy method handle 
        ->middleware('permission:products.create');

    // Chỉ User có quyền 'products.delete' mới xóa được sản phẩm
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])
        ->middleware('permission:products.delete');
    Route::post('/products/{product}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
});
