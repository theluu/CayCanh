<?php

namespace App\Http\Controllers;

use App\Exceptions\NotPurchasable;
use App\Models\Product;
use App\Services\Cart;
use App\Services\CartLine;
use App\Support\Money;
use App\Support\Seo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function index(Cart $cart): Response
    {
        $lines = $cart->lines();

        return Inertia::render('Cart', [
            'seo' => Seo::make('Giỏ hàng'),
            'lines' => self::presentLines($lines),
            'total' => Money::vnd(Cart::totalOf($lines)),
            'pruned' => $cart->pullPruned(),
        ]);
    }

    public function store(Request $request, Cart $cart): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'qty' => ['nullable', 'integer', 'min:1', 'max:'.Cart::MAX_QTY],
        ]);

        $product = Product::findOrFail($data['product_id']);

        try {
            $cart->add($product, (int) ($data['qty'] ?? 1));
        } catch (NotPurchasable $exception) {
            throw ValidationException::withMessages(['product_id' => $exception->getMessage()]);
        }

        if ($request->input('redirect') === 'cart') {
            return to_route('cart.index');
        }

        return back()->with('success', "Đã thêm \"{$product->name}\" vào giỏ hàng.");
    }

    public function update(Request $request, int $product, Cart $cart): RedirectResponse
    {
        $data = $request->validate(['qty' => ['required', 'integer', 'min:0', 'max:'.Cart::MAX_QTY]]);

        $cart->update($product, (int) $data['qty']);

        return back();
    }

    public function destroy(int $product, Cart $cart): RedirectResponse
    {
        $cart->remove($product);

        return back();
    }

    /**
     * @param  Collection<int, CartLine>  $lines
     * @return array<int, array<string, mixed>>
     */
    public static function presentLines($lines): array
    {
        return $lines->map(fn (CartLine $line) => [
            'productId' => $line->product->id,
            'name' => $line->product->name,
            'url' => $line->product->publicPath(),
            'image' => $line->product->imageUrls('gallery'),
            'qty' => $line->qty,
            'unitPrice' => Money::vnd($line->unitPrice),
            'lineTotal' => Money::vnd($line->lineTotal()),
        ])->values()->all();
    }
}
