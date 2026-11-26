<?php

use App\Enums\OrderStatus;
use App\Events\OrderPlaced;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Event;

function checkoutData(array $overrides = []): array
{
    return array_merge([
        'customer_name' => 'Nguyễn Văn A',
        'phone' => '0912 345 678',
        'email' => 'a@example.com',
        'province' => 'TP. Hồ Chí Minh',
        'address' => '12 Lê Lợi, Quận 1',
        'note' => 'Giao giờ hành chính',
        'payment_method' => 'cod',
    ], $overrides);
}

it('places an order with price snapshots and empties the cart', function () {
    Event::fake([OrderPlaced::class]);
    $product = Product::factory()->create(['name' => 'Cây Kim Tiền', 'price' => 350000]);
    $this->post('/gio-hang', ['product_id' => $product->id, 'qty' => 2]);

    $response = $this->post('/dat-hang', checkoutData());

    $order = Order::sole();
    $response->assertRedirect('/dat-hang/thanh-cong/'.$order->code);
    expect($order->code)->toMatch('/^DH-\d{6}-[A-Z0-9]{4}$/')
        ->and($order->status)->toBe(OrderStatus::New)
        ->and($order->phone)->toBe('0912345678')
        ->and($order->subtotal)->toBe(700000)
        ->and($order->items)->toHaveCount(1)
        ->and($order->items->first()->product_name)->toBe('Cây Kim Tiền')
        ->and($order->items->first()->unit_price)->toBe(350000)
        ->and(session('cart', []))->toBe([]);
    Event::assertDispatched(OrderPlaced::class, fn ($event) => $event->order->is($order));
});

it('charges the current database price, not the price when added', function () {
    $product = Product::factory()->create(['price' => 100000]);
    $this->post('/gio-hang', ['product_id' => $product->id, 'qty' => 1]);
    $product->update(['price' => 120000]);

    $this->post('/dat-hang', checkoutData());

    expect(Order::sole()->subtotal)->toBe(120000);
});

it('sends the customer back to the cart when nothing purchasable is left', function () {
    $product = Product::factory()->create();
    $this->post('/gio-hang', ['product_id' => $product->id, 'qty' => 1]);
    $product->update(['is_published' => false]);

    $this->post('/dat-hang', checkoutData())->assertRedirect('/gio-hang')->assertSessionHas('error');
    expect(Order::count())->toBe(0);
});

it('redirects the checkout page to the cart when the cart is empty', function () {
    $this->get('/dat-hang')->assertRedirect('/gio-hang');
});

it('validates required checkout fields', function () {
    $product = Product::factory()->create();
    $this->post('/gio-hang', ['product_id' => $product->id, 'qty' => 1]);

    $this->post('/dat-hang', checkoutData(['customer_name' => '', 'phone' => '123', 'address' => '', 'payment_method' => 'paypal']))
        ->assertSessionHasErrors(['customer_name', 'phone', 'address', 'payment_method']);
});

it('only shows the thank-you page for orders placed in this session', function () {
    $product = Product::factory()->create();
    $this->post('/gio-hang', ['product_id' => $product->id, 'qty' => 1]);
    $this->post('/dat-hang', checkoutData());
    $order = Order::sole();

    $this->get('/dat-hang/thanh-cong/'.$order->code)->assertOk()->assertInertia(fn ($page) => $page
        ->component('OrderThanks')
        ->where('order.code', $order->code));

    $this->flushSession();
    $this->get('/dat-hang/thanh-cong/'.$order->code)->assertNotFound();
    $this->get('/dat-hang/thanh-cong/DH-000000-ZZZZ')->assertNotFound();
});
