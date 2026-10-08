<?php
namespace WecodeGuy\ProjetMyShop\Controllers;

use WecodeGuy\ProjetMyShop\Core\Config;
use WecodeGuy\ProjetMyShop\Models\{Product, Category};

class HomeController extends Controller
{
    public function index(): void
    {
        $filters = [
            'q'        => mb_substr($this->query('q'), 0, 100),
            'category' => (int)$this->query('category', '0'),
            'min'      => $this->decimal($this->query('min')),
            'max'      => $this->decimal($this->query('max')),
            'sort'     => $this->query('sort', 'new'),
        ];
        if (!array_key_exists($filters['sort'], Product::sortOptions())) {
            $filters['sort'] = 'new';
        }

        $perPage = (int)Config::get('per_page', 8);
        $page    = max(1, (int)$this->query('page', '1'));

        $products = new Product();
        $result   = $products->search($filters, $page, $perPage);
        $pages    = max(1, (int)ceil($result['total'] / $perPage));
        if ($page > $pages) {
            $page = $pages;
            $result = $products->search($filters, $page, $perPage);
        }

        $this->view('home/index', [
            'title'      => 'Boutique',
            'products'   => $result['items'],
            'total'      => $result['total'],
            'page'       => $page,
            'pages'      => $pages,
            'filters'    => $filters,
            'categories' => (new Category())->all(),
            'sorts'      => Product::sortOptions(),
        ]);
    }
}
