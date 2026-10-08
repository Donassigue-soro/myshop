<?php
namespace WecodeGuy\ProjetMyShop\Controllers;

use WecodeGuy\ProjetMyShop\Core\Cart;
use WecodeGuy\ProjetMyShop\Models\Product;

class CartController extends Controller
{
    public function index(): void
    {
        $this->view('cart/index', ['title' => 'Mon panier'] + Cart::details());
    }

    public function add(): void
    {
        $id  = (int)$this->post('product_id');
        $qty = max(1, min(99, (int)$this->post('qty', '1')));
        $back = safe_path($_POST['return'] ?? null, '/');

        $product = $id > 0 ? (new Product())->find($id) : null;
        if (!$product) {
            $this->flash('error', "Ce produit n'existe plus.");
            redirect($back);
        }
        Cart::add($id, $qty);
        $this->flash('success', '« ' . $product['name'] . ' » ajouté au panier.');
        redirect($back);
    }

    public function update(): void
    {
        $quantities = $_POST['qty'] ?? [];
        if (is_array($quantities)) {
            foreach ($quantities as $id => $qty) {
                if (is_numeric($id) && is_numeric($qty)) {
                    Cart::set((int)$id, (int)$qty);
                }
            }
        }
        $this->flash('success', 'Panier mis à jour.');
        redirect('/cart');
    }

    public function remove(): void
    {
        Cart::remove((int)$this->post('product_id'));
        redirect('/cart');
    }
}
