<?php
namespace WecodeGuy\ProjetMyShop\Controllers;

use WecodeGuy\ProjetMyShop\Models\Product;

class ProductController extends Controller
{
    public function show(string $id): void
    {
        $product = (new Product())->find((int)$id);
        if (!$product) {
            $this->notFound('Ce produit n\'existe pas.');
            return;
        }
        $this->view('products/show', ['title' => $product['name'], 'product' => $product]);
    }
}
