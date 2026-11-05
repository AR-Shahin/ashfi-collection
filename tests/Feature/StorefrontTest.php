<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_public_pages_render(): void
    {
        foreach (['/', '/shop', '/shop?category=saree', '/shop?filter=new', '/shop?q=kurti&sort=price-asc', '/product/rosewood-chikankari-kurti',
            '/cart', '/checkout', '/wishlist', '/offers', '/track-order', '/faq', '/size-guide', '/about', '/contact', '/policy', '/terms',
            '/login', '/register', '/forgot-password', '/sitemap.xml', '/robots.txt'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/no-such-page')->assertNotFound();
    }

    public function test_old_static_links_redirect(): void
    {
        $this->get('/product.html?id=rosewood-chikankari-kurti')->assertRedirect('/product/rosewood-chikankari-kurti');
        $this->get('/shop.html?filter=new')->assertRedirect('/shop?filter=new');
        $this->get('/index.html')->assertRedirect('/');
    }

    public function test_shop_filters_by_category_and_sale(): void
    {
        $this->get('/shop?category=saree')->assertSee('Banarasi Katan Saree')->assertDontSee('Rosewood Chikankari Kurti');
        $res = $this->get('/shop?sale=1');
        Product::where('sale_price', null)->get()->each(fn ($p) => $res->assertDontSee('>'.$p->name.'<', false));
    }

    public function test_guest_can_check_out_and_track_with_phone(): void
    {
        $orna = Product::where('slug', 'embroidered-georgette-orna')->first(); // one size
        $this->postJson('/cart', ['product' => $orna->slug, 'qty' => 2])->assertOk()->assertJson(['cartCount' => 2]);

        $this->post('/checkout', [
            'name' => 'Rina Akter', 'phone' => '+88 01812-345678', 'address' => 'House 5, Road 2, Mirpur 10',
            'district' => 'Dhaka', 'area' => 'inside',
            // a tampered total is ignored – prices come from the server
            'total' => 1,
        ])->assertRedirect();

        $order = Order::with('items')->latest('id')->first();
        $this->assertSame('01812345678', $order->phone);
        $this->assertSame($orna->unitPrice() * 2, $order->subtotal);
        $this->assertSame($order->subtotal + 70, $order->total);
        $this->assertSame(2, $order->items->first()->quantity);
        $this->get('/cart')->assertSee('Your cart is empty');

        $this->post('/track-order', ['number' => strtolower($order->number), 'phone' => '01812345678'])->assertOk()->assertSee($order->number);
        $this->post('/track-order', ['number' => $order->number, 'phone' => '01700000000'])->assertSessionHas('trackError');
    }

    public function test_products_with_sizes_need_a_size(): void
    {
        $this->postJson('/cart', ['product' => 'rosewood-chikankari-kurti'])->assertStatus(422)->assertJson(['needsOptions' => true]);
        $this->postJson('/cart', ['product' => 'rosewood-chikankari-kurti', 'size' => 'M', 'color' => 'Ivory'])->assertOk();
    }

    public function test_quantity_is_capped_at_ten(): void
    {
        foreach (range(1, 4) as $i) {
            $this->postJson('/cart', ['product' => 'embroidered-georgette-orna', 'qty' => 4]);
        }
        $this->postJson('/cart', ['product' => 'embroidered-georgette-orna'])->assertJson(['cartCount' => 10]);
    }

    public function test_coupon_rules(): void
    {
        $this->postJson('/cart', ['product' => 'embroidered-georgette-orna']); // ৳1,190 – under WELCOME10's ৳1,500 minimum
        $this->postJson('/cart/coupon', ['code' => 'NOPE'])->assertStatus(422)->assertJson(['message' => 'That coupon code isn’t valid.']);
        $this->postJson('/cart/coupon', ['code' => 'welcome10'])->assertStatus(422)->assertJson(['message' => 'WELCOME10 needs a minimum spend of ৳1,500.']);

        $this->postJson('/cart', ['product' => 'banarasi-katan-saree', 'size' => 'Free Size']);
        $this->postJson('/cart/coupon', ['code' => 'welcome10'])->assertOk();
        $t = app(\App\Services\Cart::class)->totals('inside');
        $this->assertSame(500, $t->discount); // 10% would be more – capped at ৳500
    }

    public function test_customer_account_flow(): void
    {
        $this->postJson('/wishlist/rosewood-chikankari-kurti')->assertJson(['saved' => true, 'wishCount' => 1]);

        $this->post('/register', [
            'name' => 'Sadia Rahman', 'email' => 'Sadia@Example.test', 'phone' => '01912345678',
            'password' => 'secret-pass', 'password_confirmation' => 'secret-pass', 'terms' => '1',
        ])->assertRedirect('/account');
        $user = User::where('email', 'sadia@example.test')->firstOrFail();
        $this->assertCount(1, $user->wishlist); // guest wishlist kept

        $this->get('/account')->assertOk()->assertSee('Sadia');
        $this->post('/account/addresses', ['label' => 'Home', 'name' => 'Sadia Rahman', 'phone' => '01912345678', 'district' => 'Sylhet', 'address' => 'Zindabazar, Sylhet 3100'])
            ->assertRedirect('/account/addresses');
        $this->assertTrue($user->addresses()->first()->is_default);

        $this->postJson('/cart', ['product' => 'embroidered-georgette-orna']);
        $this->post('/checkout', ['name' => 'Sadia Rahman', 'phone' => '01912345678', 'address' => 'Zindabazar, Sylhet 3100', 'district' => 'Sylhet', 'area' => 'outside']);
        $order = $user->orders()->first();
        $this->assertSame(130, $order->delivery_charge);

        $this->post("/account/orders/{$order->number}/cancel")->assertRedirect();
        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->cancelled_at);

        // someone else's order is invisible
        $other = User::factory()->create();
        $this->actingAs($other)->get("/account/orders/{$order->number}")->assertNotFound();
        $this->actingAs($other)->get("/order-success/{$order->number}")->assertNotFound();
    }

    public function test_only_admins_reach_the_admin_panel(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
        $this->actingAs(User::where('is_admin', true)->first())->get('/admin')->assertOk();
        $this->get('/admin/orders')->assertOk();
    }
}
