<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Eloquent\OrderRepository;
use App\Models\Product;
use App\Policies\ProductPolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {

        // bất cứ khi nào mà ai cần ProductRepositoryInterface thì cho nó dùng
        //ProductRepository 
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        // App::singleton() tạo 1 instance như bên java
        // App::scoped() 1 instance trong 1 http request

    }

    // đã binding song an toàn để call service khác 
    public function boot(): void
    {
        Gate::policy(Product::class, ProductPolicy::class);
    }
}
