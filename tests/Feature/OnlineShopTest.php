<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnlineShopTest extends TestCase
{
    use RefreshDatabase;

    private function product(int $stock = 5, int $price = 100000): Product
    {
        $category = Category::create(['name' => 'Thiết bị', 'slug' => 'thiet-bi']);
        return Product::create([
            'category_id' => $category->id,
            'name' => 'Bàn phím demo',
            'slug' => 'ban-phim-demo',
            'price' => $price,
            'stock' => $stock,
            'is_active' => true,
        ]);
    }

    public function test_homepage_is_public(): void
    {
        $this->get('/')->assertOk()->assertSee('OpenShop');
    }

    public function test_registration_cannot_grant_admin_role(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Người mua', 'email' => 'customer@example.test',
            'password' => 'password123', 'password_confirmation' => 'password123',
            'role' => 'admin',
        ])->assertRedirect(route('home'));

        $this->assertDatabaseHas('users', ['email' => 'customer@example.test', 'role' => 'customer']);
    }

    public function test_customer_cannot_access_admin_pages(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($customer)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    }

    public function test_checkout_creates_order_and_reduces_stock(): void
    {
        $product = $this->product(stock: 5, price: 120000);
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        $this->post(route('cart.add', $product), ['quantity' => 2])->assertRedirect(route('cart.index'));
        $response = $this->post(route('checkout.store'), [
            'recipient_name' => 'Khách demo',
            'phone' => '0901234567',
            'address' => '123 đường số 1, TP.HCM',
            'note' => 'Giao giờ hành chính',
        ]);
        $order = Order::firstOrFail();
        $response->assertRedirect(route('orders.show', $order));
        $this->assertSame(240000, $order->total_amount);
        $this->assertSame('pending', $order->status);
        $this->assertSame(3, $product->fresh()->stock);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id, 'product_id' => $product->id,
            'quantity' => 2, 'subtotal' => 240000,
        ]);
        $this->assertEmpty(session('cart', []));
    }

    public function test_checkout_rejects_order_if_stock_has_dropped(): void
    {
        $product = $this->product(stock: 5);
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer)->post(route('cart.add', $product), ['quantity' => 3]);
        $product->update(['stock' => 2]);

        $this->post(route('checkout.store'), [
            'recipient_name' => 'Khách demo', 'phone' => '0901234567', 'address' => 'Địa chỉ demo',
        ])->assertSessionHasErrors('cart');
        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(2, $product->fresh()->stock);
    }

    public function test_customer_cannot_view_another_customers_order(): void
    {
        $owner = User::factory()->create(['role' => 'customer']);
        $visitor = User::factory()->create(['role' => 'customer']);
        $order = Order::create([
            'user_id' => $owner->id, 'recipient_name' => 'Owner',
            'phone' => '0901234567', 'address' => 'Demo', 'total_amount' => 100000, 'status' => 'pending',
        ]);
        $this->actingAs($visitor)->get(route('orders.show', $order))->assertForbidden();
        $this->actingAs($visitor)->patch(route('orders.cancel', $order))->assertForbidden();
    }

    public function test_cancel_pending_order_restores_stock_only_once(): void
    {
        $product = $this->product(stock: 4);
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer)->post(route('cart.add', $product), ['quantity' => 2]);
        $this->post(route('checkout.store'), [
            'recipient_name' => 'Demo', 'phone' => '0901234567', 'address' => 'Địa chỉ demo',
        ]);
        $order = Order::firstOrFail();
        $this->assertSame(2, $product->fresh()->stock);
        $this->patch(route('orders.cancel', $order))->assertRedirect();
        $this->assertSame(4, $product->fresh()->stock);
        $this->assertSame('cancelled', $order->fresh()->status);
        $this->patch(route('orders.cancel', $order))->assertRedirect();
        $this->assertSame(4, $product->fresh()->stock);
    }

    public function test_admin_cannot_skip_order_status_flow(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $buyer = User::factory()->create(['role' => 'customer']);
        $order = Order::create([
            'user_id' => $buyer->id, 'recipient_name' => 'Demo',
            'phone' => '0901234567', 'address' => 'Địa chỉ demo', 'total_amount' => 100000,
            'status' => 'pending',
        ]);
        $this->actingAs($admin)->patch(route('admin.orders.status', $order), ['status' => 'completed'])
            ->assertSessionHas('error');
        $this->assertSame('pending', $order->fresh()->status);
    }
}
