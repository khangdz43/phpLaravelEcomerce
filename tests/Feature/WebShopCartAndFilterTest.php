<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebShopCartAndFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_fetch_and_manage_cart_via_api(): void
    {
        $category = Category::create(['name' => 'Laptops', 'slug' => 'laptops']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'MacBook Pro M3',
            'slug' => 'macbook-pro-m3',
            'sku' => 'MBP-M3-01',
            'description' => 'Powerful laptop',
            'price' => 45000000,
            'stock' => 10,
            'status' => 'published',
        ]);

        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        // 1. Initial cart should be empty
        $res = $this->getJson('/cart');
        $res->assertOk()
            ->assertJsonPath('count', 0)
            ->assertJsonPath('total', 0);

        // 2. Add product with quantity 3
        $addRes = $this->postJson('/cart', [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
        $addRes->assertOk()
            ->assertJsonPath('count', 3)
            ->assertJsonPath('total', 45000000 * 3)
            ->assertJsonPath('items.0.id', $product->id)
            ->assertJsonPath('items.0.quantity', 3);

        // 3. Update quantity to 5
        $updateRes = $this->patchJson("/cart/{$product->id}", [
            'quantity' => 5,
        ]);
        $updateRes->assertOk()
            ->assertJsonPath('count', 5)
            ->assertJsonPath('total', 45000000 * 5);

        // 4. Delete item from cart
        $deleteRes = $this->deleteJson("/cart/{$product->id}");
        $deleteRes->assertOk()
            ->assertJsonPath('count', 0)
            ->assertJsonPath('total', 0);
    }

    public function test_guest_can_add_to_cart_and_get_immediate_cart_payload(): void
    {
        $category = Category::create(['name' => 'Accessories', 'slug' => 'accessories']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Vietec Hub 7-in-1',
            'slug' => 'vietec-hub-7-in-1',
            'sku' => 'HB-01',
            'price' => 850000,
            'stock' => 20,
            'status' => 'published',
        ]);

        $res = $this->postJson('/cart', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
        $res->assertOk()
          ->assertJsonPath('count', 2)
          ->assertJsonPath('total', 1700000)
          ->assertJsonPath('items.0.name', 'Vietec Hub 7-in-1')
          ->assertJsonPath('items.0.price', 850000)
          ->assertJsonPath('items.0.quantity', 2);
    }

    public function test_multi_condition_filtering_on_shop_catalog(): void
    {
        $cat1 = Category::create(['name' => 'Phím cơ', 'slug' => 'phim-co']);
        $cat2 = Category::create(['name' => 'Chuột', 'slug' => 'chuot']);

        $p1 = Product::create([
            'category_id' => $cat1->id,
            'name' => 'Bàn phím cơ không dây Vietec Pro',
            'slug' => 'ban-phim-co-vietec-pro',
            'sku' => 'KB-01',
            'description' => 'Bàn phím cơ cao cấp',
            'price' => 2500000,
            'stock' => 5,
            'status' => 'published',
        ]);

        $p2 = Product::create([
            'category_id' => $cat2->id,
            'name' => 'Chuột công thái học Vietec Master',
            'slug' => 'chuot-vietec-master',
            'sku' => 'MS-01',
            'description' => 'Chuột không dây yên tĩnh',
            'price' => 1200000,
            'stock' => 8,
            'status' => 'published',
        ]);

        // Filter by keyword & price range
        $response = $this->get('/shop?q=phím&min_price=2000000&max_price=3000000');
        $response->assertOk();
        $response->assertSee('Bàn phím cơ không dây Vietec Pro');
        $response->assertDontSee('Chuột công thái học Vietec Master');

        // Filter by category
        $catResponse = $this->get("/shop?category_id={$cat2->id}");
        $catResponse->assertOk();
        $catResponse->assertSee('Chuột công thái học Vietec Master');
        $catResponse->assertDontSee('Bàn phím cơ không dây Vietec Pro');
    }
}
