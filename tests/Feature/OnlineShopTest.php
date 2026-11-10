<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
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

    public function test_guest_is_redirected_from_private_pages(): void
    {
        $this->get(route('cart.index'))->assertRedirect(route('login'));
        $this->get(route('orders.index'))->assertRedirect(route('login'));
        $this->get(route('profile.edit'))->assertRedirect(route('login'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_logging_in_does_not_inherit_previous_session_cart(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->withSession(['cart' => [1 => 7]])
            ->post(route('login.store'), ['email' => $customer->email, 'password' => 'password'])
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($customer);
        $this->assertSame([], session('cart', []));
    }

    public function test_profile_update_does_not_allow_role_escalation(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer)
            ->patch(route('profile.update'), [
                'name' => 'Tên cập nhật', 'email' => 'profile@example.test', 'role' => 'admin',
            ])->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('users', [
            'id' => $customer->id, 'name' => 'Tên cập nhật',
            'email' => 'profile@example.test', 'role' => 'customer',
        ]);
    }

    public function test_profile_email_must_be_unique(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $other = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer)->patch(route('profile.update'), [
            'name' => 'Khách mua', 'email' => $other->email,
        ])->assertSessionHasErrors('email');
    }

    public function test_password_change_requires_current_password(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer)->patch(route('profile.password'), [
            'current_password' => 'wrong_password',
            'password' => 'new_password_123',
            'password_confirmation' => 'new_password_123',
        ])->assertSessionHasErrors('current_password');

        $this->patch(route('profile.password'), [
            'current_password' => 'password',
            'password' => 'new_password_123',
            'password_confirmation' => 'new_password_123',
        ])->assertRedirect(route('profile.edit'));

        $this->assertTrue(Hash::check('new_password_123', $customer->fresh()->password));
    }

    public function test_admin_can_create_edit_and_delete_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->post(route('admin.categories.store'), [
            'name' => 'Màn hình', 'description' => 'Các thiết bị hiển thị',
        ])->assertRedirect(route('admin.categories.index'));
        $category = Category::where('slug', 'man-hinh')->firstOrFail();

        $this->put(route('admin.categories.update', $category), [
            'name' => 'Màn hình lớn', 'description' => 'Mô tả mới',
        ])->assertRedirect(route('admin.categories.index'));
        $this->assertSame('man-hinh-lon', $category->fresh()->slug);

        $this->delete(route('admin.categories.destroy', $category))->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_admin_cannot_delete_category_containing_products(): void
    {
        $product = $this->product();
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->delete(route('admin.categories.destroy', $product->category))->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $product->category_id]);
    }

    public function test_admin_can_create_update_and_delete_product(): void
    {
        $category = Category::create(['name' => 'Phụ kiện', 'slug' => 'phu-kien']);
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->post(route('admin.products.store'), [
            'category_id' => $category->id, 'name' => 'Đế laptop',
            'description' => 'Đế nhôm', 'price' => 220000,
            'stock' => 7, 'is_active' => 1,
        ])->assertRedirect(route('admin.products.index'));
        $product = Product::where('slug', 'de-laptop')->firstOrFail();

        $this->put(route('admin.products.update', $product), [
            'category_id' => $category->id, 'name' => 'Đế laptop mới',
            'price' => 230000, 'stock' => 3, 'is_active' => 0,
        ])->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'id' => $product->id, 'price' => 230000,
            'stock' => 3, 'is_active' => 0, 'slug' => 'de-laptop-moi',
        ]);

        $this->delete(route('admin.products.destroy', $product))->assertRedirect();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_product_validation_rejects_bad_price_and_invalid_upload(): void
    {
        $category = Category::create(['name' => 'Phụ kiện', 'slug' => 'phu-kien']);
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post(route('admin.products.store'), [
            'category_id' => $category->id, 'name' => 'Máy ảnh',
            'price' => -1, 'stock' => -2,
            'image' => UploadedFile::fake()->create('script.txt', 1, 'text/plain'),
            'is_active' => 1,
        ])->assertSessionHasErrors(['price', 'stock', 'image']);
        $this->assertDatabaseCount('products', 0);
    }

    public function test_product_image_upload_is_saved_to_public_disk(): void
    {
        Storage::fake('public');
        $category = Category::create(['name' => 'Thiết bị', 'slug' => 'thiet-bi']);
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post(route('admin.products.store'), [
            'category_id' => $category->id, 'name' => 'Máy ảnh web',
            'price' => 400000, 'stock' => 5,
            'image' => UploadedFile::fake()->image('demo.jpg'),
            'is_active' => 1,
        ])->assertRedirect(route('admin.products.index'));

        $product = Product::where('slug', 'may-anh-web')->firstOrFail();
        $this->assertNotNull($product->image_path);
        Storage::disk('public')->assertExists($product->image_path);
    }

    public function test_product_cannot_be_deleted_while_a_pending_order_uses_it(): void
    {
        $product = $this->product();
        $buyer = User::factory()->create(['role' => 'customer']);
        $order = Order::create([
            'user_id' => $buyer->id, 'recipient_name' => 'Buyer',
            'phone' => '0901234567', 'address' => 'Địa chỉ',
            'total_amount' => 100000, 'status' => 'pending',
        ]);
        $order->items()->create([
            'product_id' => $product->id, 'product_name' => $product->name,
            'quantity' => 1, 'price' => 100000, 'subtotal' => 100000,
        ]);

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->delete(route('admin.products.destroy', $product))->assertSessionHas('error');
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_customer_cannot_use_administration_actions_or_export_orders(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $category = Category::create(['name' => 'Thiết bị', 'slug' => 'thiet-bi']);
        $this->actingAs($customer)
            ->post(route('admin.products.store'), [
                'category_id' => $category->id, 'name' => 'Không được phép',
                'price' => 10000, 'stock' => 10, 'is_active' => 1,
            ])->assertForbidden();
        $this->get(route('admin.orders.export'))->assertForbidden();
        $this->assertDatabaseCount('products', 0);
    }

    public function test_filter_products_by_stock_level(): void
    {
        $low = $this->product(stock: 2);
        $category = $low->category;
        $out = Product::create([
            'category_id' => $category->id, 'name' => 'Chuột hết hàng',
            'slug' => 'chuot-het-hang', 'price' => 100000,
            'stock' => 0, 'is_active' => true,
        ]);
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('admin.products.index', ['stock' => 'out']))
            ->assertOk()->assertSee($out->name)->assertDontSee($low->name);
        $this->get(route('admin.products.index', ['stock' => 'low']))
            ->assertOk()->assertSee($low->name)->assertDontSee($out->name);
    }

    public function test_inactive_product_is_not_publicly_visible(): void
    {
        $product = $this->product();
        $product->update(['is_active' => false]);
        $this->get(route('products.show', $product->slug))->assertNotFound();
        $this->get(route('home'))->assertDontSee($product->name);
    }

    public function test_admin_can_export_filtered_orders_without_spreadsheet_injection(): void
    {
        $buyer = User::factory()->create(['role' => 'customer']);
        $pending = Order::create([
            'user_id' => $buyer->id, 'recipient_name' => '=SUM(1,2)',
            'phone' => '0901234567', 'address' => 'Địa chỉ',
            'total_amount' => 130000, 'status' => 'pending',
        ]);
        $completed = Order::create([
            'user_id' => $buyer->id, 'recipient_name' => 'Hoàn tất',
            'phone' => '0901234567', 'address' => 'Địa chỉ',
            'total_amount' => 90000, 'status' => 'completed',
        ]);
        $response = $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('admin.orders.export', ['status' => 'pending']));

        $response->assertOk();
        $this->assertStringContainsString('.csv', $response->headers->get('content-disposition'));
        $csv = $response->streamedContent();
        $this->assertStringContainsString($pending->code, $csv);
        $this->assertStringNotContainsString($completed->code, $csv);
        $this->assertStringContainsString("'=SUM(1,2)", $csv);
    }

    public function test_admin_cancel_after_confirmation_restores_stock_once(): void
    {
        $product = $this->product(stock: 6);
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer)->post(route('cart.add', $product), ['quantity' => 3]);
        $this->post(route('checkout.store'), [
            'recipient_name' => 'Khách mua', 'phone' => '0901234567',
            'address' => 'Địa chỉ nhận hàng',
        ]);
        $order = Order::firstOrFail();
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->patch(route('admin.orders.status', $order), ['status' => 'confirmed'])->assertSessionHas('success');
        $this->patch(route('admin.orders.status', $order), ['status' => 'cancelled'])->assertSessionHas('success');
        $this->assertSame(6, $product->fresh()->stock);
        $this->patch(route('admin.orders.status', $order), ['status' => 'cancelled'])->assertSessionHas('error');
        $this->assertSame(6, $product->fresh()->stock);
    }

    public function test_admin_order_flow_and_completed_revenue(): void
    {
        $buyer = User::factory()->create(['role' => 'customer']);
        $order = Order::create([
            'user_id' => $buyer->id, 'recipient_name' => 'Demo',
            'phone' => '0901234567', 'address' => 'Địa chỉ demo',
            'total_amount' => 250000, 'status' => 'pending',
        ]);
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        foreach (['confirmed', 'shipping', 'completed'] as $status) {
            $this->patch(route('admin.orders.status', $order), ['status' => $status])
                ->assertSessionHas('success');
            $this->assertSame($status, $order->fresh()->status);
        }
        $this->get(route('admin.dashboard'))->assertOk()->assertSee('250.000');
    }


    public function test_ajax_add_to_cart_updates_count_without_page_reload(): void
    {
        $product = $this->product(stock: 6);
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer)->postJson(route('cart.add', $product), ['quantity' => 3])
            ->assertOk()->assertJson(['cart_count' => 3]);
        $this->assertSame(3, session('cart')[$product->id]);
    }

    public function test_ajax_add_to_cart_rejects_stock_overflow(): void
    {
        $product = $this->product(stock: 1);
        $this->actingAs(User::factory()->create(['role' => 'customer']))
            ->postJson(route('cart.add', $product), ['quantity' => 2])
            ->assertUnprocessable()->assertJsonValidationErrors('cart');
        $this->assertSame([], session('cart', []));
    }

}
