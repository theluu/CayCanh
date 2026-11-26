<?php

use App\Models\Inquiry;
use App\Models\Order;
use App\Models\Product;
use App\Services\Cart;

function safetyCheckout(array $overrides = []): array
{
    return array_merge([
        'customer_name' => 'Nguyễn Văn A', 'phone' => '0912345678', 'province' => 'Hà Nội',
        'address' => '1 Hàng Bài', 'payment_method' => 'cod',
    ], $overrides);
}

it('does not place a partial order when an item disappeared, and tells the customer', function () {
    $kept = Product::factory()->create();
    $hidden = Product::factory()->create(['name' => 'Cây vừa ẩn']);
    $this->post('/gio-hang', ['product_id' => $kept->id, 'qty' => 1]);
    $this->post('/gio-hang', ['product_id' => $hidden->id, 'qty' => 1]);
    $hidden->update(['is_published' => false]);

    $this->post('/dat-hang', safetyCheckout())
        ->assertRedirect('/dat-hang')
        ->assertSessionHas('error', fn (string $message) => str_contains($message, 'Cây vừa ẩn'));

    expect(Order::count())->toBe(0);

    $this->post('/dat-hang', safetyCheckout())->assertRedirect();
    expect(Order::sole()->items)->toHaveCount(1);
});

it('lists removed items on the checkout page', function () {
    $kept = Product::factory()->create();
    $hidden = Product::factory()->create(['name' => 'Cây vừa ẩn']);
    $this->post('/gio-hang', ['product_id' => $kept->id, 'qty' => 1]);
    $this->post('/gio-hang', ['product_id' => $hidden->id, 'qty' => 1]);
    $hidden->update(['is_published' => false]);
    app()->forgetScopedInstances();

    $this->get('/dat-hang')->assertInertia(fn ($page) => $page->component('Checkout')->where('pruned', ['Cây vừa ẩn']));
});

it('keeps the removed-items notice in the session until a page shows it', function () {
    $kept = Product::factory()->create();
    $hidden = Product::factory()->create(['name' => 'Cây vừa ẩn']);
    $this->post('/gio-hang', ['product_id' => $kept->id, 'qty' => 1]);
    $this->post('/gio-hang', ['product_id' => $hidden->id, 'qty' => 1]);
    $hidden->update(['is_published' => false]);

    app(Cart::class)->lines();
    app()->forgetScopedInstances();

    $this->get('/gio-hang')->assertInertia(fn ($page) => $page->where('pruned', ['Cây vừa ẩn']));
    app()->forgetScopedInstances();
    $this->get('/gio-hang')->assertInertia(fn ($page) => $page->where('pruned', []));
});

it('asks the customer to confirm when the total changed after they saw it', function () {
    $product = Product::factory()->create(['price' => 100000]);
    $this->post('/gio-hang', ['product_id' => $product->id, 'qty' => 1]);
    $this->get('/dat-hang')->assertInertia(fn ($page) => $page->where('totalRaw', 100000));
    $product->update(['price' => 130000]);

    $this->post('/dat-hang', safetyCheckout(['expected_total' => 100000]))
        ->assertRedirect('/dat-hang')
        ->assertSessionHas('error');
    expect(Order::count())->toBe(0);

    $this->post('/dat-hang', safetyCheckout(['expected_total' => 130000]))->assertRedirect();
    expect(Order::sole()->subtotal)->toBe(130000);
});

it('counts checkout and inquiry submissions separately for rate limiting', function () {
    $product = Product::factory()->create();
    $this->post('/gio-hang', ['product_id' => $product->id, 'qty' => 1]);
    $this->post('/dat-hang', safetyCheckout());

    foreach (range(1, 5) as $i) {
        $this->post('/lien-he', ['type' => 'contact', 'name' => 'B', 'phone' => '0912345678'])->assertRedirect();
    }

    expect(Inquiry::count())->toBe(5);
});

it('returns throttled inertia form posts to the form with an error instead of an error page', function () {
    foreach (range(1, 5) as $i) {
        $this->post('/lien-he', ['type' => 'contact', 'name' => 'B', 'phone' => '0912345678']);
    }

    $this->from('/lien-he')
        ->withHeaders(['X-Inertia' => 'true'])
        ->post('/lien-he', ['type' => 'contact', 'name' => 'B', 'phone' => '0912345678'])
        ->assertRedirect('/lien-he')
        ->assertSessionHasErrors('throttle');
});
