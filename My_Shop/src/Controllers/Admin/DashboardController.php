<?php
namespace WecodeGuy\ProjetMyShop\Controllers\Admin;

use WecodeGuy\ProjetMyShop\Models\{Product, Category, User, Order};

class DashboardController extends AdminController
{
    public function index(): void
    {
        $this->view('admin/dashboard', [
            'title'      => 'Administration',
            'section'    => 'dashboard',
            'products'   => (new Product())->count(),
            'categories' => (new Category())->count(),
            'users'      => (new User())->count(),
            'orders'     => (new Order())->stats(),
        ]);
    }
}
