<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. RBAC - Roles & Permissions phải chạy trước
            RbacSeeder::class,

            // 2. Users - Admin, Staff, Customers
            UserSeeder::class,

            // 3. Categories - Danh mục sản phẩm
            CategorySeeder::class,

            // 4. Products - Sản phẩm mẫu
            ProductSeeder::class,

            // 5. Coupons - Mã giảm giá
            CouponSeeder::class,
        ]);
    }
}
