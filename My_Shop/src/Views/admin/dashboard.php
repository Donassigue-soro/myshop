<?php \WecodeGuy\ProjetMyShop\Core\View::partial('partials/admin_nav', ['section' => $section]); ?>
<main class="page">
    <h1>Tableau de bord</h1>
    <div class="stats">
        <a class="cadre stat" href="<?= url('/admin/products') ?>"><strong><?= (int)$products ?></strong><span>Produits</span></a>
        <a class="cadre stat" href="<?= url('/admin/categories') ?>"><strong><?= (int)$categories ?></strong><span>Catégories</span></a>
        <a class="cadre stat" href="<?= url('/admin/users') ?>"><strong><?= (int)$users ?></strong><span>Utilisateurs</span></a>
        <a class="cadre stat" href="<?= url('/admin/orders') ?>"><strong><?= (int)$orders['count'] ?></strong><span>Commandes</span></a>
        <div class="cadre stat"><strong><?= e(money($orders['revenue'])) ?></strong><span>Chiffre d'affaires (hors annulées)</span></div>
    </div>
</main>
