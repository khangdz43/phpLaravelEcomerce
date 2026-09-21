<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\CatalogController;
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\AccountController;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\ApiDocsController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\CommentController;

Route::get('/', function () {
    return redirect()->route('shop.index');
});

Route::get('/shop', [CatalogController::class, 'index'])->name('shop.index');
Route::get('/shop/products/{product}', [CatalogController::class, 'show'])->name('shop.products.show');
Route::post('/shop/products/{product}/comments', [CommentController::class, 'store'])->middleware('auth')->name('shop.comments.store');
Route::get('/api-docs', ApiDocsController::class)->name('api.docs');

// Cart Web Routes
Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{productId}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{productId}', [CartController::class, 'destroy'])->name('cart.destroy');

Route::get('/checkout', [CheckoutController::class, 'create'])->name('shop.checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('shop.checkout.store');
Route::post('/checkout/coupon-preview', [CheckoutController::class, 'previewCoupon'])->name('shop.checkout.coupon');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('shop.checkout.success');
Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('login.store');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'storeRegistration'])->name('register.store');
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');
Route::middleware('auth')->group(function (): void {
    Route::get('/account/orders', [AccountController::class, 'orders'])->name('account.orders');
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::patch('/admin/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.status');
});

