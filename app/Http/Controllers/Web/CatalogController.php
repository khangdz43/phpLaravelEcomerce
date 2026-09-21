<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function __construct(private readonly ProductService $productService) {}

    public function index(Request $request): View
    {
        return view('shop.index', [
            'products' => $this->productService->searchPublished($request->only([
                'q',
                'category_id',
                'min_price',
                'max_price',
                'sort',
                'per_page',
            ])),
            'catalogTotal' => Product::query()->count(),
        ]);
    }

    public function show(Product $product): View
    {
        abort_unless($product->status === 'published', 404);

        return view('shop.show', [
            'product' => $product->load(['category', 'comments.user', 'images']),
        ]);
    }
}
