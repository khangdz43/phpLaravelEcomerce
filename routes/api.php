<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\Admin\AdminOrderController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Authentication
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Categories (Public Browse)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

// Products (Public Browse)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

// Product Comments/Reviews (Public Read)
Route::get('/products/{product}/comments', [CommentController::class, 'index']);


/*
|--------------------------------------------------------------------------
| Authenticated User Routes (auth:sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Profile & Logout
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Shopping Cart
    Route::get('/cart', [CartController::class, 'show']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{product}', [CartController::class, 'update']);
    Route::delete('/cart/{product}', [CartController::class, 'destroy']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);

    // User Addresses
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::put('/addresses/{address}', [AddressController::class, 'update']);
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy']);
    Route::post('/addresses/{address}/set-default', [AddressController::class, 'setDefault']);

    // Coupons
    Route::post('/coupons/validate', [CouponController::class, 'validateCoupon']);

    // Product Comments/Reviews (Auth user create/delete)
    Route::post('/products/{product}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Admin / Staff Management Routes (Permission-protected)
    |--------------------------------------------------------------------------
    */
    // Category management
    Route::post('/categories', [CategoryController::class, 'store'])->middleware('permission:products.create');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->middleware('permission:products.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->middleware('permission:products.delete');

    // Product management
    Route::post('/products', [ProductController::class, 'store'])->middleware('permission:products.create');
    Route::put('/products/{product}', [ProductController::class, 'update'])->middleware('permission:products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->middleware('permission:products.delete');

    // Admin Order Management
    Route::get('/admin/orders', [AdminOrderController::class, 'index'])->middleware('permission:orders.view');
    Route::get('/admin/orders/{order}', [AdminOrderController::class, 'show'])->middleware('permission:orders.view');
    Route::put('/admin/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->middleware('permission:orders.update');
});
