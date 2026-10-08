<?php
namespace WecodeGuy\ProjetMyShop\Core;

use WecodeGuy\ProjetMyShop\Models\Product;

/** Panier stocké en session : [product_id => quantité]. Les prix sont TOUJOURS relus en base. */
class Cart
{
    private const MAX_QTY = 99;

    private static function all(): array
    {
        return $_SESSION['cart'] ?? [];
    }

    public static function add(int $productId, int $qty = 1): void
    {
        $cart = self::all();
        $cart[$productId] = min(self::MAX_QTY, ($cart[$productId] ?? 0) + max(1, $qty));
        $_SESSION['cart'] = $cart;
    }

    public static function set(int $productId, int $qty): void
    {
        if ($qty <= 0) {
            self::remove($productId);
            return;
        }
        $_SESSION['cart'][$productId] = min(self::MAX_QTY, $qty);
    }

    public static function remove(int $productId): void
    {
        unset($_SESSION['cart'][$productId]);
    }

    public static function clear(): void
    {
        unset($_SESSION['cart']);
    }

    public static function count(): int
    {
        return array_sum(self::all());
    }

    /** @return array{lines: array, total: float} */
    public static function details(): array
    {
        $cart = self::all();
        if (!$cart) {
            return ['lines' => [], 'total' => 0.0];
        }

        $products = (new Product())->findMany(array_keys($cart));
        $lines = [];
        $total = 0.0;
        foreach ($products as $p) {
            $qty = (int)$cart[$p['id']];
            $subtotal = (float)$p['price'] * $qty;
            $total += $subtotal;
            $lines[] = ['product' => $p, 'qty' => $qty, 'subtotal' => $subtotal];
        }

        // Nettoie les produits supprimés entre-temps
        $existing = array_column($products, 'id');
        foreach (array_keys($cart) as $id) {
            if (!in_array($id, $existing)) {
                unset($_SESSION['cart'][$id]);
            }
        }
        return ['lines' => $lines, 'total' => $total];
    }
}
