<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::whereNotNull('parent_id')->get()->keyBy('slug');

        $products = [
            // Điện thoại
            [
                'category_slug' => 'dien-thoai',
                'items' => [
                    [
                        'name' => 'iPhone 15 Pro Max 256GB',
                        'slug' => 'iphone-15-pro-max-256gb',
                        'sku' => 'IPH15PM256',
                        'description' => 'iPhone 15 Pro Max với chip A17 Pro mạnh mẽ, camera 48MP chuyên nghiệp, màn hình Super Retina XDR 6.7 inch và khung titanium cao cấp.',
                        'price' => 34990000,
                        'sale_price' => 31990000,
                        'stock' => 50,
                        'status' => 'published',
                    ],
                    [
                        'name' => 'Samsung Galaxy S24 Ultra 512GB',
                        'slug' => 'samsung-galaxy-s24-ultra-512gb',
                        'sku' => 'SSS24U512',
                        'description' => 'Samsung Galaxy S24 Ultra với bút S Pen tích hợp, camera 200MP, chip Snapdragon 8 Gen 3 và màn hình Dynamic AMOLED 6.8 inch.',
                        'price' => 31990000,
                        'sale_price' => null,
                        'stock' => 35,
                        'status' => 'published',
                    ],
                    [
                        'name' => 'Xiaomi 14 Pro 512GB',
                        'slug' => 'xiaomi-14-pro-512gb',
                        'sku' => 'XM14P512',
                        'description' => 'Xiaomi 14 Pro với chip Snapdragon 8 Gen 3, camera Leica, sạc nhanh HyperCharge 120W và màn hình LTPO AMOLED 6.73 inch.',
                        'price' => 22990000,
                        'sale_price' => 19990000,
                        'stock' => 60,
                        'status' => 'published',
                    ],
                    [
                        'name' => 'OPPO Find X7 Ultra',
                        'slug' => 'oppo-find-x7-ultra',
                        'sku' => 'OPX7U',
                        'description' => 'OPPO Find X7 Ultra với chip Dimensity 9300, camera Hasselblad periscope zoom 6x và sạc nhanh SuperVOOC 100W.',
                        'price' => 26490000,
                        'sale_price' => null,
                        'stock' => 25,
                        'status' => 'published',
                    ],
                ],
            ],
            // Laptop
            [
                'category_slug' => 'laptop-may-tinh',
                'items' => [
                    [
                        'name' => 'MacBook Air M3 15 inch 16GB/512GB',
                        'slug' => 'macbook-air-m3-15-inch',
                        'sku' => 'MBAM315-512',
                        'description' => 'MacBook Air M3 với chip Apple M3, màn hình Liquid Retina 15.3 inch, 16GB RAM và 512GB SSD. Hiệu năng vượt trội, pin lên đến 18 tiếng.',
                        'price' => 38990000,
                        'sale_price' => 35990000,
                        'stock' => 20,
                        'status' => 'published',
                    ],
                    [
                        'name' => 'Dell XPS 15 9530 Core i9-13900H',
                        'slug' => 'dell-xps-15-9530-i9',
                        'sku' => 'DXPS159530',
                        'description' => 'Dell XPS 15 với Core i9-13900H, 32GB DDR5, RTX 4070 8GB, màn hình OLED 15.6 inch 3.5K và thiết kế cao cấp nhỏ gọn.',
                        'price' => 52990000,
                        'sale_price' => null,
                        'stock' => 10,
                        'status' => 'published',
                    ],
                    [
                        'name' => 'ASUS ROG Zephyrus G16 RTX 4080',
                        'slug' => 'asus-rog-zephyrus-g16-rtx4080',
                        'sku' => 'ASROG4080',
                        'description' => 'ASUS ROG Zephyrus G16 gaming laptop với Core i9-13980HX, RTX 4080 16GB, 32GB DDR5 và màn hình QHD 240Hz.',
                        'price' => 69990000,
                        'sale_price' => 64990000,
                        'stock' => 8,
                        'status' => 'published',
                    ],
                    [
                        'name' => 'Lenovo ThinkPad X1 Carbon Gen 11',
                        'slug' => 'lenovo-thinkpad-x1-carbon-gen11',
                        'sku' => 'LNVX1CG11',
                        'description' => 'ThinkPad X1 Carbon Gen 11 siêu nhẹ 1.12kg với Core i7-1365U, 16GB LPDDR5, 512GB SSD và bảo mật doanh nghiệp cao cấp.',
                        'price' => 42990000,
                        'sale_price' => null,
                        'stock' => 15,
                        'status' => 'published',
                    ],
                ],
            ],
            // Phụ kiện điện tử
            [
                'category_slug' => 'phu-kien-dien-tu',
                'items' => [
                    [
                        'name' => 'Apple AirPods Pro 2nd Generation',
                        'slug' => 'apple-airpods-pro-2nd-gen',
                        'sku' => 'APAP2G',
                        'description' => 'AirPods Pro thế hệ 2 với chip H2, chống ồn chủ động ANC, âm thanh Spatial Audio và hộp sạc MagSafe.',
                        'price' => 6490000,
                        'sale_price' => 5790000,
                        'stock' => 100,
                        'status' => 'published',
                    ],
                    [
                        'name' => 'Sony WH-1000XM5 Wireless Headphones',
                        'slug' => 'sony-wh1000xm5',
                        'sku' => 'SNYWH1KXM5',
                        'description' => 'Sony WH-1000XM5 với chống ồn xuất sắc nhất trong lớp, âm thanh LDAC Hi-Res, 30 giờ pin và thiết kế gập gọn.',
                        'price' => 8990000,
                        'sale_price' => 7490000,
                        'stock' => 45,
                        'status' => 'published',
                    ],
                    [
                        'name' => 'Logitech MX Master 3S Chuột không dây',
                        'slug' => 'logitech-mx-master-3s',
                        'sku' => 'LGTMXM3S',
                        'description' => 'Chuột không dây cao cấp Logitech MX Master 3S với cuộn trang MagSpeed, DPI 8000, kết nối đa thiết bị và pin 70 ngày.',
                        'price' => 2490000,
                        'sale_price' => null,
                        'stock' => 80,
                        'status' => 'published',
                    ],
                    [
                        'name' => 'Anker USB-C Hub 10-in-1',
                        'slug' => 'anker-usb-c-hub-10in1',
                        'sku' => 'ANKRHUB10',
                        'description' => 'Hub USB-C 10 cổng Anker: 4K HDMI, 100W PD, USB-A 3.0, SD/MicroSD, Ethernet và 3.5mm audio jack.',
                        'price' => 1290000,
                        'sale_price' => 990000,
                        'stock' => 120,
                        'status' => 'published',
                    ],
                ],
            ],
            // Thời trang Nam
            [
                'category_slug' => 'thoi-trang-nam',
                'items' => [
                    [
                        'name' => 'Áo Polo Nam Cổ Trụ Premium Cotton',
                        'slug' => 'ao-polo-nam-premium-cotton',
                        'sku' => 'APOLONM01',
                        'description' => 'Áo polo nam chất liệu 100% cotton Premium, thoáng mát, form dáng slim-fit hiện đại. Nhiều màu sắc đa dạng.',
                        'price' => 450000,
                        'sale_price' => 350000,
                        'stock' => 200,
                        'status' => 'published',
                    ],
                    [
                        'name' => 'Quần Jean Nam Straight Fit Basic',
                        'slug' => 'quan-jean-nam-straight-fit',
                        'sku' => 'QJNMSF01',
                        'description' => 'Quần jean nam dáng straight fit, chất vải denim cao cấp co giãn tốt, đường may chắc chắn, phù hợp nhiều phong cách.',
                        'price' => 690000,
                        'sale_price' => null,
                        'stock' => 150,
                        'status' => 'published',
                    ],
                ],
            ],
            // Thời trang Nữ
            [
                'category_slug' => 'thoi-trang-nu',
                'items' => [
                    [
                        'name' => 'Đầm Maxi Hoa Nhí Vintage',
                        'slug' => 'dam-maxi-hoa-nhi-vintage',
                        'sku' => 'DMAXHVT01',
                        'description' => 'Đầm maxi chất liệu voan mềm mại, họa tiết hoa nhí vintage, phù hợp đi biển, dã ngoại hay dạo phố.',
                        'price' => 520000,
                        'sale_price' => 420000,
                        'stock' => 100,
                        'status' => 'published',
                    ],
                    [
                        'name' => 'Áo Blazer Nữ Công Sở Linen',
                        'slug' => 'ao-blazer-nu-cong-so-linen',
                        'sku' => 'ABLZNUCSL01',
                        'description' => 'Áo blazer nữ chất linen nhẹ nhàng, form dáng oversized thanh lịch, phù hợp công sở hoặc dạo phố.',
                        'price' => 780000,
                        'sale_price' => null,
                        'stock' => 80,
                        'status' => 'published',
                    ],
                ],
            ],
            // Giày dép
            [
                'category_slug' => 'giay-dep',
                'items' => [
                    [
                        'name' => 'Nike Air Max 270 React',
                        'slug' => 'nike-air-max-270-react',
                        'sku' => 'NKAM270R',
                        'description' => 'Nike Air Max 270 React với đế Air lớn nhất trong lịch sử, công nghệ React foam êm ái và thiết kế thời thượng.',
                        'price' => 3490000,
                        'sale_price' => 2990000,
                        'stock' => 60,
                        'status' => 'published',
                    ],
                    [
                        'name' => 'Adidas Ultraboost 22',
                        'slug' => 'adidas-ultraboost-22',
                        'sku' => 'ADUB22',
                        'description' => 'Adidas Ultraboost 22 với công nghệ Boost energy return, đế Primeknit+ ôm chân và thiết kế chạy bộ hiệu năng cao.',
                        'price' => 3990000,
                        'sale_price' => null,
                        'stock' => 45,
                        'status' => 'published',
                    ],
                ],
            ],
            // Đồ gia dụng
            [
                'category_slug' => 'do-gia-dung',
                'items' => [
                    [
                        'name' => 'Nồi Chiên Không Dầu Philips HD9252 4.1L',
                        'slug' => 'noi-chien-khong-dau-philips-hd9252',
                        'sku' => 'PHNCKHD9252',
                        'description' => 'Nồi chiên không dầu Philips HD9252 dung tích 4.1L, công nghệ Rapid Air giúp thức ăn chín đều, giòn ngon mà không cần dầu.',
                        'price' => 2890000,
                        'sale_price' => 2390000,
                        'stock' => 35,
                        'status' => 'published',
                    ],
                    [
                        'name' => 'Máy Lọc Không Khí Xiaomi 4 Pro',
                        'slug' => 'may-loc-khong-khi-xiaomi-4-pro',
                        'sku' => 'XMLKK4P',
                        'description' => 'Máy lọc không khí Xiaomi 4 Pro với bộ lọc HEPA 3 lớp, lọc bụi mịn PM2.5, vi khuẩn và allergen trong diện tích 48m2.',
                        'price' => 4990000,
                        'sale_price' => 3990000,
                        'stock' => 25,
                        'status' => 'published',
                    ],
                ],
            ],
            // Sách
            [
                'category_slug' => 'sach',
                'items' => [
                    [
                        'name' => 'Đắc Nhân Tâm - Dale Carnegie',
                        'slug' => 'dac-nhan-tam-dale-carnegie',
                        'sku' => 'BKDNT001',
                        'description' => 'Cuốn sách kinh điển về nghệ thuật giao tiếp và tạo dựng mối quan hệ. Bán chạy nhất mọi thời đại với hơn 30 triệu bản in.',
                        'price' => 119000,
                        'sale_price' => 89000,
                        'stock' => 500,
                        'status' => 'published',
                    ],
                    [
                        'name' => 'Nhà Giả Kim - Paulo Coelho',
                        'slug' => 'nha-gia-kim-paulo-coelho',
                        'sku' => 'BKNGK001',
                        'description' => 'Tiểu thuyết huyền thoại về hành trình tìm kiếm kho báu và ý nghĩa cuộc sống. Dịch ra 80 thứ tiếng, bán 65 triệu bản.',
                        'price' => 99000,
                        'sale_price' => null,
                        'stock' => 300,
                        'status' => 'published',
                    ],
                    [
                        'name' => 'Clean Code - Robert C. Martin',
                        'slug' => 'clean-code-robert-martin',
                        'sku' => 'BKCC001',
                        'description' => 'Sách lập trình kinh điển về cách viết code sạch, dễ đọc và bảo trì. Bắt buộc phải đọc cho mọi lập trình viên chuyên nghiệp.',
                        'price' => 350000,
                        'sale_price' => 280000,
                        'stock' => 150,
                        'status' => 'published',
                    ],
                ],
            ],
            // Thể thao
            [
                'category_slug' => 'dung-cu-the-thao',
                'items' => [
                    [
                        'name' => 'Tạ Tay Điều Chỉnh 30kg PowerBlock',
                        'slug' => 'ta-tay-dieu-chinh-30kg-powerblock',
                        'sku' => 'TTTC30PB',
                        'description' => 'Bộ tạ tay điều chỉnh PowerBlock thay thế 15 cặp tạ truyền thống. Điều chỉnh từ 2.5-30kg, tiết kiệm không gian và chi phí.',
                        'price' => 4990000,
                        'sale_price' => null,
                        'stock' => 20,
                        'status' => 'published',
                    ],
                    [
                        'name' => 'Thảm Yoga TPE Chống Trượt 6mm',
                        'slug' => 'tham-yoga-tpe-chong-truot-6mm',
                        'sku' => 'THYGTPE6',
                        'description' => 'Thảm yoga chất liệu TPE thân thiện môi trường, dày 6mm chống trượt tốt, nhẹ dễ cuộn và mang theo.',
                        'price' => 450000,
                        'sale_price' => 380000,
                        'stock' => 80,
                        'status' => 'published',
                    ],
                ],
            ],
        ];

        $count = 0;
        foreach ($products as $group) {
            $category = $categories->get($group['category_slug']);
            if (!$category) continue;

            foreach ($group['items'] as $item) {
                $product = Product::firstOrCreate(
                    ['slug' => $item['slug']],
                    array_merge($item, ['category_id' => $category->id])
                );

                // Add sample images
                if ($product->wasRecentlyCreated && $product->images()->count() === 0) {
                    $imageNumber = ($count % 20) + 1;
                    $product->images()->createMany([
                        [
                            'image_url' => "https://picsum.photos/seed/{$product->slug}/800/800",
                            'alt_text' => $product->name,
                            'sort_order' => 1,
                            'is_primary' => true,
                        ],
                        [
                            'image_url' => "https://picsum.photos/seed/{$product->slug}-2/800/800",
                            'alt_text' => $product->name . ' - Ảnh 2',
                            'sort_order' => 2,
                            'is_primary' => false,
                        ],
                    ]);
                }

                $count++;
            }
        }

        $this->command->info("✅ ProductSeeder: Tạo {$count} sản phẩm thành công.");
    }
}
