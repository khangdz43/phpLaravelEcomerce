<?php

namespace App\Http\Controllers\Web;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\CouponFormRequest;
use App\Http\Requests\Web\ProductFormRequest;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use App\Services\ProductService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
        private readonly OrderService $orderService,
    ) {}

    public function index(Request $request): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'products' => Product::query()->count(),
                'orders' => Order::query()->count(),
                'revenue' => (int) Order::query()->where('status', '!=', OrderStatus::CANCELLED->value)->sum('total_amount'),
                'customers' => User::query()->count(),
            ],
            'products' => Product::query()->with('category')->latest()->paginate(8, ['*'], 'products_page'),
            'orders' => Order::query()->with('items')->latest()->paginate(8, ['*'], 'orders_page'),
            'coupons' => Coupon::query()->latest()->limit(8)->get(),
        ]);
    }

    public function createProduct(): View
    {
        return view('admin.product-form', [
            'product' => new Product(['status' => 'published', 'stock' => 0, 'price' => 0]),
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function storeProduct(ProductFormRequest $request): RedirectResponse
    {
        $this->productService->create($request->validated());

        return redirect()->route('admin.dashboard')->with('success', 'Đã tạo sản phẩm.');
    }

    public function editProduct(Product $product): View
    {
        return view('admin.product-form', [
            'product' => $product,
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function updateProduct(ProductFormRequest $request, Product $product): RedirectResponse
    {
        $this->productService->update($product, $request->validated());

        return redirect()->route('admin.dashboard')->with('success', 'Đã cập nhật sản phẩm.');
    }

    public function destroyProduct(Product $product): RedirectResponse
    {
        abort_unless(request()->user()?->hasPermission('products.delete'), 403);
        $this->productService->deleteById($product->id);

        return back()->with('success', 'Đã xóa sản phẩm.');
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->hasPermission('products.create'), 403);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        Category::query()->create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']).'-'.Str::lower(Str::random(4)),
            'is_active' => true,
        ]);

        return back()->with('success', 'Đã thêm danh mục.');
    }

    public function storeCoupon(CouponFormRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['code'] = strtoupper($data['code']);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['minimum_order_amount'] = $data['minimum_order_amount'] ?? 0;
        Coupon::query()->create($data);

        return back()->with('success', 'Đã tạo mã giảm giá.');
    }

    public function updateOrderStatus(Request $request, Order $order): RedirectResponse
    {
        abort_unless($request->user()?->hasPermission('orders.update') || $request->user()?->hasPermission('orders.view'), 403);
        $validated = $request->validate(['status' => ['required', 'in:'.implode(',', array_column(OrderStatus::cases(), 'value'))]]);
        $this->orderService->updateStatus($order, $validated['status']);

        return back()->with('success', 'Trạng thái đơn hàng đã được cập nhật.');
    }
}
