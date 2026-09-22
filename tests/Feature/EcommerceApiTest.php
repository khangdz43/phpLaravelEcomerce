<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EcommerceApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacSeeder::class);
    }

    public function test_product_can_be_resolved_by_slug(): void
    {
        $product = Product::factory()->create([
            'slug' => 'learning-laptop',
            'status' => 'published',
        ]);

        $this->getJson('/api/products/learning-laptop')
            ->assertOk()
            ->assertJsonPath('data.slug', $product->slug);
    }

    public function test_product_detail_can_be_resolved_by_id(): void
    {
        $product = Product::factory()->create(['status' => 'published']);

        $this->getJson("/api/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $product->id);
    }

    public function test_staff_can_update_products_through_policy_and_permission(): void
    {
        $staff = User::where('email', 'staff@gmail.com')->firstOrFail();
        $product = Product::factory()->create(['status' => 'published']);
        Sanctum::actingAs($staff);

        $this->putJson("/api/products/{$product->id}", ['name' => 'Updated product'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated product');
    }

    public function test_authenticated_user_can_create_a_polymorphic_product_comment(): void
    {
        $user = User::where('email', 'customer@gmail.com')->firstOrFail();
        $product = Product::factory()->create(['status' => 'published']);
        Sanctum::actingAs($user);

        $this->postJson("/api/products/{$product->slug}/comments", [
            'body' => 'This is a useful learning product.',
        ])->assertCreated()
            ->assertJsonPath('data.commentable_id', $product->id)
            ->assertJsonPath('data.user.id', $user->id);

        $this->assertDatabaseHas('comments', [
            'commentable_type' => Product::class,
            'commentable_id' => $product->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_customer_cannot_update_a_product(): void
    {
        $customer = User::where('email', 'customer@gmail.com')->firstOrFail();
        $product = Product::factory()->create(['status' => 'published']);
        Sanctum::actingAs($customer);

        $this->putJson("/api/products/{$product->id}", ['name' => 'Blocked update'])
            ->assertForbidden();
    }

    public function test_authenticated_api_can_read_its_order_history(): void
    {
        $customer = User::where('email', 'customer@gmail.com')->firstOrFail();
        Sanctum::actingAs($customer);

        $this->getJson('/api/orders')
            ->assertOk()
            ->assertJsonStructure(['data' => ['items', 'pagination']]);
    }

    public function test_admin_can_read_dashboard_statistics(): void
    {
        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();
        Sanctum::actingAs($admin);

        $this->getJson('/api/admin/dashboard')
            ->assertOk()
            ->assertJsonStructure(['data' => [
                'orders',
                'revenue' => ['completed', 'today'],
                'catalog' => ['products', 'published_products', 'low_stock_products', 'out_of_stock_products'],
                'customers',
            ]]);
    }

    public function test_staff_can_filter_admin_products_and_update_stock(): void
    {
        $staff = User::where('email', 'staff@gmail.com')->firstOrFail();
        $product = Product::factory()->create(['stock' => 2, 'status' => 'published']);
        Sanctum::actingAs($staff);

        $this->getJson('/api/admin/products/low-stock')
            ->assertOk()
            ->assertJsonPath('data.items.0.id', $product->id);

        $this->patchJson("/api/admin/products/{$product->id}/stock", ['stock' => 20])
            ->assertOk()
            ->assertJsonPath('data.stock', 20);
    }

    public function test_admin_can_search_users_by_role(): void
    {
        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();
        Sanctum::actingAs($admin);

        $this->getJson('/api/admin/users?role=customer&per_page=1')
            ->assertOk()
            ->assertJsonPath('data.items.0.roles.0', 'customer')
            ->assertJsonStructure(['data' => ['items', 'pagination']]);
    }

    public function test_user_can_register_and_login(): void
    {
        $this->postJson('/api/auth/register', [
            'name' => 'New Customer',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated()
            ->assertJsonPath('data.user.email', 'new@example.com')
            ->assertJsonStructure(['data' => ['access_token', 'token_type']]);

        $this->postJson('/api/auth/login', [
            'email' => 'new@example.com',
            'password' => 'password123',
        ])->assertOk()
            ->assertJsonStructure(['data' => ['access_token', 'user']]);
    }

    public function test_invalid_login_is_rejected(): void
    {
        $this->postJson('/api/auth/login', [
            'email' => 'admin@gmail.com',
            'password' => 'wrong-password',
        ])->assertUnauthorized();
    }

    public function test_user_can_add_update_and_remove_cart_item(): void
    {
        $user = User::where('email', 'customer@gmail.com')->firstOrFail();
        $product = Product::factory()->create(['status' => 'published', 'price' => 100000]);
        Sanctum::actingAs($user);

        $this->postJson('/api/cart', [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertCreated()
            ->assertJsonPath('data.items.0.quantity', 2);

        $this->putJson("/api/cart/{$product->id}", ['quantity' => 3])
            ->assertOk()
            ->assertJsonPath('data.items.0.quantity', 3);

        $this->deleteJson("/api/cart/{$product->id}")
            ->assertOk()
            ->assertJsonPath('data.items', []);
    }

    public function test_user_can_manage_only_its_own_addresses(): void
    {
        $user = User::where('email', 'customer@gmail.com')->firstOrFail();
        $otherUser = User::where('email', 'staff@gmail.com')->firstOrFail();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/addresses', [
            'recipient_name' => 'Nguyen Van A',
            'phone' => '0987654321',
            'address_line' => '123 Nguyen Hue',
            'province' => 'Ho Chi Minh',
            'is_default' => true,
        ])->assertCreated();

        $address = Address::where('user_id', $user->id)->firstOrFail();
        $response->assertJsonPath('data.recipient_name', 'Nguyen Van A');

        $this->putJson("/api/addresses/{$address->id}", [
            'address_line' => '456 Le Loi',
        ])->assertOk()
            ->assertJsonPath('data.address_line', '456 Le Loi');

        $otherAddress = Address::create([
            'user_id' => $otherUser->id,
            'recipient_name' => 'Other User',
            'phone' => '0123456789',
            'address_line' => 'Other Street',
            'province' => 'Ha Noi',
        ]);

        $this->deleteJson("/api/addresses/{$otherAddress->id}")
            ->assertForbidden();
    }

    public function test_user_can_validate_coupon(): void
    {
        $user = User::where('email', 'customer@gmail.com')->firstOrFail();
        Coupon::create([
            'code' => 'SAVE10',
            'type' => 'percent',
            'value' => 10,
            'minimum_order_amount' => 100000,
            'is_active' => true,
        ]);
        Sanctum::actingAs($user);

        $this->postJson('/api/coupons/validate', [
            'code' => 'save10',
            'order_amount' => 500000,
        ])->assertOk()
            ->assertJsonPath('data.discount_amount', 50000)
            ->assertJsonPath('data.final_total', 450000);
    }

    public function test_user_can_create_order_and_stock_is_decreased(): void
    {
        $user = User::where('email', 'customer@gmail.com')->firstOrFail();
        $product = Product::factory()->create([
            'status' => 'published',
            'price' => 100000,
            'stock' => 5,
        ]);
        Sanctum::actingAs($user);

        $this->postJson('/api/orders', [
            'customer_name' => 'Nguyen Van A',
            'customer_email' => 'customer@example.com',
            'customer_phone' => '0987654321',
            'shipping_address' => '123 Nguyen Hue',
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
            'payment_method' => 'cod',
        ])->assertCreated()
            ->assertJsonPath('data.customer.name', 'Nguyen Van A')
            ->assertJsonPath('data.items.0.quantity', 2);

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);
        $this->assertDatabaseHas('orders', ['user_id' => $user->id, 'status' => OrderStatus::PENDING->value]);
    }

    public function test_customer_can_cancel_own_pending_order(): void
    {
        $user = User::where('email', 'customer@gmail.com')->firstOrFail();
        $order = Order::create([
            'order_code' => 'ORD-TEST-001',
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => '0987654321',
            'shipping_address' => '123 Nguyen Hue',
            'subtotal_amount' => 100000,
            'discount_amount' => 0,
            'total_amount' => 100000,
            'status' => OrderStatus::PENDING->value,
            'payment_method' => 'cod',
        ]);
        Sanctum::actingAs($user);

        $this->postJson("/api/orders/{$order->order_code}/cancel")
            ->assertOk()
            ->assertJsonPath('data.status', OrderStatus::CANCELLED->value);
    }

    public function test_admin_can_update_order_status(): void
    {
        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();
        $order = Order::create([
            'order_code' => 'ORD-TEST-002',
            'customer_name' => 'Customer',
            'customer_email' => 'customer@example.com',
            'customer_phone' => '0987654321',
            'shipping_address' => '123 Nguyen Hue',
            'subtotal_amount' => 100000,
            'discount_amount' => 0,
            'total_amount' => 100000,
            'status' => OrderStatus::PENDING->value,
            'payment_method' => 'cod',
        ]);
        Sanctum::actingAs($admin);

        $this->putJson("/api/admin/orders/{$order->order_code}/status", [
            'status' => OrderStatus::PROCESSING->value,
        ])->assertOk()
            ->assertJsonPath('data.status', OrderStatus::PROCESSING->value);
    }

    public function test_customer_cannot_access_admin_product_list(): void
    {
        $customer = User::where('email', 'customer@gmail.com')->firstOrFail();
        Sanctum::actingAs($customer);

        $this->getJson('/api/admin/products')->assertForbidden();
    }

    public function test_admin_can_create_and_delete_a_product(): void
    {
        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();
        Sanctum::actingAs($admin);

        $category = \App\Models\Category::factory()->create();
        $response = $this->postJson('/api/products', [
            'category_id' => $category->id,
            'name' => 'Test Product',
            'sku' => 'TEST-PRODUCT-001',
            'price' => 100000,
            'stock' => 10,
            'description' => 'Product for feature testing.',
            'status' => 'published',
        ])->assertCreated();

        $productId = $response->json('data.id');

        $this->deleteJson("/api/products/{$productId}")
            ->assertOk();

        $this->assertDatabaseMissing('products', ['id' => $productId]);
    }

    public function test_staff_cannot_delete_a_product_without_delete_permission(): void
    {
        $staff = User::where('email', 'staff@gmail.com')->firstOrFail();
        $product = Product::factory()->create(['status' => 'published']);
        Sanctum::actingAs($staff);

        // cố tính sửa test CI
        $this->deleteJson("/api/products/{$product->id}")->assertForbidden();
    }

    public function test_admin_can_manage_categories(): void
    {
        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/categories', [
            'name' => 'Test Category',
            'description' => 'Category for feature testing.',
            'is_active' => true,
        ])->assertCreated();

        $categoryId = $response->json('data.id');

        $this->putJson("/api/categories/{$categoryId}", [
            'name' => 'Updated Test Category',
        ])->assertOk()
            ->assertJsonPath('data.name', 'Updated Test Category');

        $this->deleteJson("/api/categories/{$categoryId}")
            ->assertOk();

        $this->assertDatabaseMissing('categories', ['id' => $categoryId]);
    }

    public function test_customer_cannot_create_a_category(): void
    {
        $customer = User::where('email', 'customer@gmail.com')->firstOrFail();
        Sanctum::actingAs($customer);

        $this->postJson('/api/categories', ['name' => 'Blocked Category'])
            ->assertForbidden();
    }
}
