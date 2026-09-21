<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Eloquent\OrderRepository;
use App\Models\Product;
use App\Policies\ProductPolicy;
use App\Models\Category;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {

        // bất cứ khi nào mà ai cần ProductRepositoryInterface thì cho nó dùng
        //ProductRepository 
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
    }

    public function boot(): void
    {
        Gate::policy(Product::class, ProductPolicy::class);

        Blade::directive(
            'money',
            fn(string $expression) =>
            "<?php echo number_format({$expression}, 0, ',', '.') . ' đ'; ?>"
        );

        View::composer('components.layouts.shop', function ($view): void {
            $view->with('navigationCategories', Category::query()
                ->where('is_active', true)
                ->withCount('products')
                ->orderBy('name')
                ->get());
            $view->with('isStaff', auth()->user()?->hasPermission('orders.view') ?? false);
        });
    }
}
