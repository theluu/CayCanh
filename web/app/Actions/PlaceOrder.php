<?php

namespace App\Actions;

use App\Events\OrderPlaced;
use App\Exceptions\EmptyCart;
use App\Models\Order;
use App\Rules\VietnamesePhone;
use App\Services\Cart;
use App\Services\CartLine;
use Illuminate\Support\Facades\DB;

class PlaceOrder
{
    /**
     * @param  array{customer_name: string, phone: string, email?: ?string, province: string, address: string, note?: ?string, payment_method: string}  $data
     *
     * @throws EmptyCart when no purchasable line is left
     */
    public function handle(array $data, Cart $cart): Order
    {
        $lines = $cart->lines();

        if ($lines->isEmpty()) {
            throw new EmptyCart('Giỏ hàng trống hoặc sản phẩm không còn bán.');
        }

        $order = DB::transaction(function () use ($data, $lines) {
            $order = Order::create([
                'code' => Order::generateCode(),
                'customer_name' => $data['customer_name'],
                'phone' => VietnamesePhone::normalize($data['phone']),
                'email' => $data['email'] ?? null,
                'province' => $data['province'],
                'address' => $data['address'],
                'note' => $data['note'] ?? null,
                'payment_method' => $data['payment_method'],
                'subtotal' => $lines->sum(fn (CartLine $line) => $line->lineTotal()),
            ]);

            $order->items()->createMany($lines->map(fn (CartLine $line) => [
                'product_id' => $line->product->id,
                'product_name' => $line->product->name,
                'unit_price' => $line->unitPrice,
                'qty' => $line->qty,
                'line_total' => $line->lineTotal(),
            ])->all());

            return $order;
        });

        $cart->clear();

        OrderPlaced::dispatch($order);

        return $order;
    }
}
