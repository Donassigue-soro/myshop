<?php
namespace WecodeGuy\ProjetMyShop\Controllers\Admin;

use WecodeGuy\ProjetMyShop\Core\Uploader;
use WecodeGuy\ProjetMyShop\Models\{Product, Category};

class ProductAdminController extends AdminController
{
    public function index(): void
    {
        $perPage = 15;
        $page = max(1, (int)$this->query('page', '1'));
        $q = mb_substr($this->query('q'), 0, 100);

        $result = (new Product())->search(['q' => $q, 'sort' => 'new'], $page, $perPage);
        $this->view('admin/products/index', [
            'title' => 'Produits', 'section' => 'products',
            'products' => $result['items'], 'total' => $result['total'],
            'page' => $page, 'pages' => max(1, (int)ceil($result['total'] / $perPage)), 'q' => $q,
        ]);
    }

    public function create(): void
    {
        $this->form(['name' => '', 'description' => '', 'price' => '', 'category_id' => null, 'image_path' => null], [], false);
    }

    public function store(): void
    {
        [$data, $errors] = $this->validated();
        $image = Uploader::image($_FILES['image'] ?? null, $uploadError);
        if ($uploadError) {
            $errors['image'] = $uploadError;
        }
        if ($errors) {
            if ($image) { Uploader::delete($image); }
            $this->form($data + ['image_path' => null], $errors, false);
            return;
        }
        (new Product())->create($data, $image);
        $this->flash('success', 'Produit créé.');
        redirect('/admin/products');
    }

    public function edit(string $id): void
    {
        $product = (new Product())->find((int)$id);
        if (!$product) {
            $this->notFound('Produit introuvable.');
            return;
        }
        $this->form($product, [], true);
    }

    public function update(string $id): void
    {
        $products = new Product();
        $product  = $products->find((int)$id);
        if (!$product) {
            $this->notFound('Produit introuvable.');
            return;
        }

        [$data, $errors] = $this->validated();
        $image = Uploader::image($_FILES['image'] ?? null, $uploadError);
        if ($uploadError) {
            $errors['image'] = $uploadError;
        }
        if ($errors) {
            if ($image) { Uploader::delete($image); }
            $this->form($data + ['id' => $product['id'], 'image_path' => $product['image_path']], $errors, true);
            return;
        }

        $products->update((int)$product['id'], $data, $image);
        if ($image) {
            Uploader::delete($product['image_path']);   // supprime l'ancienne image
        }
        $this->flash('success', 'Produit modifié.');
        redirect('/admin/products');
    }

    public function delete(string $id): void
    {
        $products = new Product();
        $product  = $products->find((int)$id);
        if ($product) {
            $products->delete((int)$product['id']);
            Uploader::delete($product['image_path']);
            $this->flash('success', 'Produit supprimé.');
        }
        redirect('/admin/products');
    }

    // ------------------------------------------------------------------
    private function form(array $product, array $errors, bool $isEdit): void
    {
        if ($errors) {
            http_response_code(422);
        }
        $this->view('admin/products/form', [
            'title' => $isEdit ? 'Modifier le produit' : 'Nouveau produit', 'section' => 'products',
            'product' => $product, 'errors' => $errors, 'isEdit' => $isEdit,
            'categories' => (new Category())->all(),
        ]);
    }

    /** @return array{0: array, 1: array} [données nettoyées, erreurs] */
    private function validated(): array
    {
        $errors = [];
        $name = $this->post('name');
        $description = $this->post('description');
        $priceRaw = $this->post('price');
        $price = $this->decimal($priceRaw);
        $catId = (int)$this->post('category_id');

        if ($name === '' || mb_strlen($name) > 150) {
            $errors['name'] = 'Le nom est obligatoire (150 caractères maximum).';
        }
        if (mb_strlen($description) > 5000) {
            $errors['description'] = 'Description trop longue (5000 caractères maximum).';
        }
        if ($price === null || $price > 99999999) {
            $errors['price'] = 'Prix invalide.';
        }
        $category = null;
        if ($catId > 0) {
            $category = (new Category())->find($catId);
            if (!$category) {
                $errors['category_id'] = 'Catégorie inconnue.';
            }
        }

        return [[
            'name' => $name, 'description' => $description,
            'price' => $price !== null ? number_format($price, 2, '.', '') : $priceRaw,
            'category_id' => $category ? (int)$category['id'] : null,
        ], $errors];
    }
}
