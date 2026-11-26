<?php

use App\Models\Product;

it('adds a standard product to the cart', function () {
    $product = Product::factory()->create(['price' => 200000]);

    $this->post('/gio-hang', ['product_id' => $product->id, 'qty' => 2])->assertRedirect();

    expect(session('cart'))->toBe([$product->id => 2]);
    $this->get('/gio-hang')->assertInertia(fn ($page) => $page
        ->component('Cart')
        ->where('cart.count', 2)
        ->where('lines.0.lineTotal', '400.000 ₫')
        ->where('total', '400.000 ₫'));
});

it('accumulates quantity when the same product is added twice', function () {
    $product = Product::factory()->create();

    $this->post('/gio-hang', ['product_id' => $product->id, 'qty' => 1]);
    $this->post('/gio-hang', ['product_id' => $product->id, 'qty' => 3]);

    expect(session('cart'))->toBe([$product->id => 4]);
});

it('refuses to put an art tree into the cart', function () {
    $art = Product::factory()->art()->create(['price' => 9000000, 'show_price' => true]);

    $this->postJson('/gio-hang', ['product_id' => $art->id, 'qty' => 1])->assertUnprocessable();
    $this->post('/gio-hang', ['product_id' => $art->id, 'qty' => 1])->assertSessionHasErrors('product_id');

    expect(session('cart', []))->toBe([]);
});

it('caps the quantity at 99', function () {
    $product = Product::factory()->create();

    $this->post('/gio-hang', ['product_id' => $product->id, 'qty' => 80]);
    $this->post('/gio-hang', ['product_id' => $product->id, 'qty' => 80]);

    expect(session('cart'))->toBe([$product->id => 99]);
});

it('updates and removes cart lines', function () {
    $product = Product::factory()->create();
    $this->post('/gio-hang', ['product_id' => $product->id, 'qty' => 2]);

    $this->patch('/gio-hang/'.$product->id, ['qty' => 5]);
    expect(session('cart'))->toBe([$product->id => 5]);

    $this->patch('/gio-hang/'.$product->id, ['qty' => 0]);
    expect(session('cart'))->toBe([]);

    $this->post('/gio-hang', ['product_id' => $product->id, 'qty' => 1]);
    $this->delete('/gio-hang/'.$product->id);
    expect(session('cart'))->toBe([]);
});

it('reprices lines from the database and drops products that became unavailable', function () {
    $kept = Product::factory()->create(['price' => 100000]);
    $hidden = Product::factory()->create(['name' => 'Cây bị ẩn', 'price' => 100000]);
    $this->post('/gio-hang', ['product_id' => $kept->id, 'qty' => 1]);
    $this->post('/gio-hang', ['product_id' => $hidden->id, 'qty' => 1]);

    $kept->update(['price' => 150000]);
    $hidden->update(['is_published' => false]);

    $this->get('/gio-hang')->assertInertia(fn ($page) => $page
        ->has('lines', 1)
        ->where('lines.0.unitPrice', '150.000 ₫')
        ->where('pruned', ['Cây bị ẩn']));
    expect(session('cart'))->toBe([$kept->id => 1]);
});
