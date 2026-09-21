<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Điện tử & Công nghệ',
                'slug' => 'dien-tu-cong-nghe',
                'description' => 'Điện thoại, laptop, máy tính bảng và các thiết bị điện tử khác',
                'is_active' => true,
                'children' => [
                    ['name' => 'Điện thoại', 'slug' => 'dien-thoai', 'is_active' => true],
                    ['name' => 'Laptop & Máy tính', 'slug' => 'laptop-may-tinh', 'is_active' => true],
                    ['name' => 'Máy tính bảng', 'slug' => 'may-tinh-bang', 'is_active' => true],
                    ['name' => 'Phụ kiện điện tử', 'slug' => 'phu-kien-dien-tu', 'is_active' => true],
                ],
            ],
            [
                'name' => 'Thời trang',
                'slug' => 'thoi-trang',
                'description' => 'Quần áo, giày dép, túi xách và phụ kiện thời trang',
                'is_active' => true,
                'children' => [
                    ['name' => 'Thời trang Nam', 'slug' => 'thoi-trang-nam', 'is_active' => true],
                    ['name' => 'Thời trang Nữ', 'slug' => 'thoi-trang-nu', 'is_active' => true],
                    ['name' => 'Giày dép', 'slug' => 'giay-dep', 'is_active' => true],
                ],
            ],
            [
                'name' => 'Nhà cửa & Đời sống',
                'slug' => 'nha-cua-doi-song',
                'description' => 'Đồ gia dụng, nội thất và trang trí nhà cửa',
                'is_active' => true,
                'children' => [
                    ['name' => 'Đồ gia dụng', 'slug' => 'do-gia-dung', 'is_active' => true],
                    ['name' => 'Nội thất', 'slug' => 'noi-that', 'is_active' => true],
                ],
            ],
            [
                'name' => 'Sách & Văn phòng phẩm',
                'slug' => 'sach-van-phong-pham',
                'description' => 'Sách, tài liệu và dụng cụ văn phòng',
                'is_active' => true,
                'children' => [
                    ['name' => 'Sách', 'slug' => 'sach', 'is_active' => true],
                    ['name' => 'Văn phòng phẩm', 'slug' => 'van-phong-pham', 'is_active' => true],
                ],
            ],
            [
                'name' => 'Thể thao & Du lịch',
                'slug' => 'the-thao-du-lich',
                'description' => 'Dụng cụ thể thao, thiết bị dã ngoại và du lịch',
                'is_active' => true,
                'children' => [
                    ['name' => 'Dụng cụ thể thao', 'slug' => 'dung-cu-the-thao', 'is_active' => true],
                    ['name' => 'Du lịch & Dã ngoại', 'slug' => 'du-lich-da-ngoai', 'is_active' => true],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $parent = Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );

            foreach ($children as $childData) {
                Category::firstOrCreate(
                    ['slug' => $childData['slug']],
                    array_merge($childData, ['parent_id' => $parent->id])
                );
            }
        }

        $this->command->info('✅ CategorySeeder: Tạo categories thành công.');
    }
}
