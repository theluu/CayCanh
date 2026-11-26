<?php

namespace App\Services;

use App\Exceptions\NotPurchasable;
use App\Models\Product;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;

/**
 * Session-backed cart storing only [product_id => qty]; prices always come from the database.
 */
class Cart
{
    public const MAX_QTY = 99;

    private const KEY = 'cart';

    private const PRUNED_KEY = 'cart_pruned';

    public function __construct(private Session $session) {}

    public function add(Product $product, int $qty = 1): void
    {
        if (! $product->isPurchasable()) {
            throw new NotPurchasable("Sản phẩm \"{$product->name}\" không thể đặt mua online.");
        }

        $items = $this->items();
        $items[$product->id] = min(self::MAX_QTY, ($items[$product->id] ?? 0) + max(1, $qty));
        $this->store($items);
    }

    public function update(int $productId, int $qty): void
    {
        $items = $this->items();

        if (! array_key_exists($productId, $items)) {
            return;
        }

        if ($qty <= 0) {
            unset($items[$productId]);
        } else {
            $items[$productId] = min(self::MAX_QTY, $qty);
        }

        $this->store($items);
    }

    public function remove(int $productId): void
    {
        $this->update($productId, 0);
    }

    public function clear(): void
    {
        $this->session->forget([self::KEY, self::PRUNED_KEY]);
    }

    public function count(): int
    {
        return (int) array_sum($this->items());
    }

    /**
     * Reloads every product, drops those that can no longer be bought and remembers their names.
     *
     * @return Collection<int, CartLine>
     */
    public function lines(): Collection
    {
        $items = $this->items();

        if ($items === []) {
            return collect();
        }

        $products = Product::with('media')->whereKey(array_keys($items))->get()->keyBy('id');
        $lines = collect();
        $kept = [];

        foreach ($items as $productId => $qty) {
            $product = $products->get($productId);

            if (! $product || ! $product->isPurchasable()) {
                if ($product) {
                    $this->session->push(self::PRUNED_KEY, $product->name);
                }

                continue;
            }

            $kept[$productId] = $qty;
            $lines->push(new CartLine($product, $qty, (int) $product->effectivePrice()));
        }

        if ($kept !== $items) {
            $this->store($kept);
        }

        return $lines;
    }

    /**
     * @param  Collection<int, CartLine>  $lines
     */
    public static function totalOf(Collection $lines): int
    {
        return $lines->sum(fn (CartLine $line) => $line->lineTotal());
    }

    public function hasPruned(): bool
    {
        return $this->session->has(self::PRUNED_KEY);
    }

    /**
     * Names of products removed from the cart since a page last showed them; reading clears them.
     *
     * @return array<int, string>
     */
    public function pullPruned(): array
    {
        return array_values(array_unique((array) $this->session->pull(self::PRUNED_KEY, [])));
    }

    /** @return array<int, int> */
    private function items(): array
    {
        return array_map('intval', (array) $this->session->get(self::KEY, []));
    }

    /** @param  array<int, int>  $items */
    private function store(array $items): void
    {
        $this->session->put(self::KEY, $items);
    }
}
