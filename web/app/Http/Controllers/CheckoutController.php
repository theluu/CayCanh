<?php

namespace App\Http\Controllers;

use App\Actions\PlaceOrder;
use App\Enums\PaymentMethod;
use App\Exceptions\EmptyCart;
use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Cart;
use App\Support\Money;
use App\Support\Seo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function create(Cart $cart): Response|RedirectResponse
    {
        $lines = $cart->lines();

        if ($lines->isEmpty()) {
            return to_route('cart.index');
        }

        return Inertia::render('Checkout', [
            'seo' => Seo::make('Đặt hàng'),
            'lines' => CartController::presentLines($lines),
            'total' => Money::vnd(Cart::totalOf($lines)),
            'totalRaw' => Cart::totalOf($lines),
            'pruned' => $cart->pullPruned(),
            'paymentMethods' => collect(PaymentMethod::cases())->map(fn (PaymentMethod $method) => ['value' => $method->value, 'label' => $method->label()])->all(),
        ]);
    }

    public function store(CheckoutRequest $request, Cart $cart, PlaceOrder $placeOrder): RedirectResponse
    {
        $lines = $cart->lines();

        // Never place a different order than the one the customer reviewed.
        if ($lines->isNotEmpty() && $cart->hasPruned()) {
            return to_route('checkout.create')->with('error', 'Một số sản phẩm không còn bán và đã được bỏ khỏi giỏ: '
                .implode(', ', $cart->pullPruned()).'. Vui lòng kiểm tra lại đơn hàng.');
        }

        if ($request->filled('expected_total') && (int) $request->input('expected_total') !== Cart::totalOf($lines)) {
            return to_route('checkout.create')->with('error', 'Giá sản phẩm vừa được cập nhật. Vui lòng kiểm tra lại tổng tiền trước khi đặt hàng.');
        }

        try {
            $order = $placeOrder->handle($request->validated(), $cart);
        } catch (EmptyCart $exception) {
            return to_route('cart.index')->with('error', 'Sản phẩm trong giỏ không còn bán, vui lòng kiểm tra lại giỏ hàng.');
        }

        $request->session()->push('placed_orders', $order->code);

        return redirect('/dat-hang/thanh-cong/'.$order->code);
    }

    public function thanks(Request $request, string $code): Response
    {
        abort_unless(in_array($code, (array) $request->session()->get('placed_orders', []), true), 404);

        $order = Order::with('items')->where('code', $code)->firstOrFail();

        return Inertia::render('OrderThanks', [
            'seo' => Seo::make('Đặt hàng thành công'),
            'order' => [
                'code' => $order->code,
                'customerName' => $order->customer_name,
                'phone' => $order->phone,
                'address' => $order->address.', '.$order->province,
                'paymentMethod' => $order->payment_method->label(),
                'total' => $order->formattedSubtotal(),
                'items' => $order->items->map(fn (OrderItem $item) => [
                    'name' => $item->product_name,
                    'qty' => $item->qty,
                    'lineTotal' => Money::vnd($item->line_total),
                ])->all(),
            ],
        ]);
    }
}
