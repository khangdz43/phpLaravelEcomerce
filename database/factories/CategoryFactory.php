<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        // Fake một tên danh mục ngẫu nhiên (2 từ)
        // mỗi lần chạy thì ko trùng 
        $name = fake()->unique()->words(2, true);

        return [
            'parent_id' => null, // Mặc định là danh mục gốc
            // viết hoa chữ đầu
            'name'      => ucfirst($name),
            'slug'      => Str::slug($name), // Tự biến "ao nam" thành "ao-nam"
            'is_active' => true,
        ];
    }
}
