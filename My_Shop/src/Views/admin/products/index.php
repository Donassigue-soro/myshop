<?php use WecodeGuy\ProjetMyShop\Core\View; View::partial('partials/admin_nav', ['section' => $section]); ?>
<main class="page">
    <div class="page-head">
        <h1>Produits <small>(<?= (int)$total ?>)</small></h1>
        <a href="<?= url('/admin/products/create') ?>" class="btn">+ Nouveau produit</a>
    </div>

    <form method="get" action="<?= url('/admin/products') ?>" class="inline-search">
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Rechercher…" aria-label="Recherche">
        <button type="submit" class="btn btn-outline">Rechercher</button>
    </form>

    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>Image</th><th>Nom</th><th>Catégorie</th><th>Prix</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><img src="<?= e(product_image($p['image_path'])) ?>" alt="" class="thumb"></td>
                <td><?= e($p['name']) ?></td>
                <td><?= e($p['category_name'] ?? '—') ?></td>
                <td><?= e(money($p['price'])) ?></td>
                <td class="row-actions">
                    <a href="<?= url('/admin/products/' . (int)$p['id'] . '/edit') ?>">Modifier</a>
                    <form method="post" action="<?= url('/admin/products/' . (int)$p['id'] . '/delete') ?>" class="inline-form" data-confirm="Supprimer « <?= e($p['name']) ?> » ?">
                        <?= csrf_field() ?>
                        <button type="submit" class="link-button danger">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$products): ?><tr><td colspan="5" class="empty">Aucun produit.</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</main>
<?php View::partial('partials/pagination', ['page' => $page, 'pages' => $pages, 'basePath' => '/admin/products']); ?>
