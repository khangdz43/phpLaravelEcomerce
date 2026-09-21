<?php

namespace Tests\Feature;

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

    public function test_staff_can_update_products_through_policy_and_permission(): void
    {
        $staff = User::where('email', 'staff@gmail.com')->firstOrFail();
        $product = Product::factory()->create(['status' => 'published']);
        Sanctum::actingAs($staff);

        $this->putJson("/api/products/{$product->slug}", ['name' => 'Updated product'])
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

        $this->putJson("/api/products/{$product->slug}", ['name' => 'Blocked update'])
            ->assertForbidden();
    }

    public function test_guest_can_complete_web_checkout_and_stock_is_decremented(): void
    {
        $product = Product::factory()->create([
            'status' => 'published',
            'stock' => 5,
            'price' => 250000,
        ]);

        $response = $this->post('/checkout', [
            'customer_name' => 'Test Customer',
            'customer_email' => 'checkout@example.com',
            'customer_phone' => '0900000000',
            'shipping_address' => '1 Learning Street',
            'items' => json_encode([
                ['id' => $product->id, 'quantity' => 2, 'price' => 1],
            ]),
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $order = \App\Models\Order::where('customer_email', 'checkout@example.com')->firstOrFail();

        $response->assertRedirect(route('shop.checkout.success', $order->order_code));
        $this->assertSame(3, $product->fresh()->stock);
        $this->assertSame(500000, (int) $order->total_amount);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_staff_can_sign_in_and_open_operations_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'staff@gmail.com',
            'password' => '12345678',
        ]);

        $response->assertRedirect(route('shop.index'));
        $this->get('/admin')->assertOk()->assertSee('Quản trị');
    }

    public function test_authenticated_api_can_read_its_order_history(): void
    {
        $customer = User::where('email', 'customer@gmail.com')->firstOrFail();
        Sanctum::actingAs($customer);

        $this->getJson('/api/orders')
            ->assertOk()
            ->assertJsonStructure(['data' => ['items', 'pagination']]);
    }

    public function test_guest_can_register_from_the_web_and_get_customer_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'New Web Customer',
            'email' => 'new-web-customer@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('shop.index'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'new-web-customer@example.com']);
        $this->assertTrue(auth()->user()->roles()->where('name', 'customer')->exists());
    }
}
