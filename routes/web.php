<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\CatalogController;

Route::get('/', function () {
    return redirect()->route('shop.index');
});

Route::get('/shop', [CatalogController::class, 'index'])->name('shop.index');
Route::get('/shop/products/{product}', [CatalogController::class, 'show'])->name('shop.products.show');
