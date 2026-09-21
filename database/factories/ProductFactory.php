<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            // Lấy ngẫu nhiên ID của 1 Category đã có sẵn trong DB , nếu không thì cho là null 
            // nếu null tự động gọi Category::factory() để tạo mới ngay 1 Category rồi lấy ID đó gán vào category_id.
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'name'        => ucfirst($name),
            'slug'        => Str::slug($name),
            'sku'         => 'SKU-' . strtoupper(Str::random(6)), // VD: SKU-A8X9K2
            'description' => fake()->paragraph(),
            'price'       => fake()->numberBetween(100000, 2000000), // Giá từ 100k đến 2 triệu VNĐ
            'sale_price'  => null,
            'stock'       => fake()->numberBetween(0, 100),
            'status'      => fake()->randomElement(['draft', 'published', 'out_of_stock']),
        ];
    }
}
