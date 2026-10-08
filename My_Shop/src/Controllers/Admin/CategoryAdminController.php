<?php
namespace WecodeGuy\ProjetMyShop\Controllers\Admin;

use WecodeGuy\ProjetMyShop\Models\Category;

class CategoryAdminController extends AdminController
{
    public function index(): void
    {
        $this->view('admin/categories/index', [
            'title' => 'Catégories', 'section' => 'categories', 'categories' => (new Category())->all(),
        ]);
    }

    public function store(): void
    {
        $name = $this->post('name');
        $parentId = (int)$this->post('parent_id');
        $categories = new Category();

        if ($name === '' || mb_strlen($name) > 100) {
            $this->flash('error', 'Nom de catégorie invalide (100 caractères maximum).');
            redirect('/admin/categories');
        }
        $parent = null;
        if ($parentId > 0) {
            $parent = $categories->find($parentId);
            // une seule profondeur : le parent doit être une catégorie racine
            if (!$parent || $parent['parent_id'] !== null) {
                $this->flash('error', 'Catégorie parente invalide.');
                redirect('/admin/categories');
            }
        }
        $categories->create($name, $parent ? (int)$parent['id'] : null);
        $this->flash('success', 'Catégorie créée.');
        redirect('/admin/categories');
    }

    public function delete(string $id): void
    {
        (new Category())->delete((int)$id);
        $this->flash('success', 'Catégorie supprimée (ses produits sont conservés, sans catégorie).');
        redirect('/admin/categories');
    }
}
