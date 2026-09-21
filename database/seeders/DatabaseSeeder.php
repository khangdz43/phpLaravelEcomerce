<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Chạy Seeder phân quyền RBAC trước
        $this->call([
            RbacSeeder::class,
        ]);

        // 2. Tạo Data mẫu Category & Product
        Category::factory(5)->create();
        Product::factory(20)->create();
    }
}
