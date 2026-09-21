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
}
