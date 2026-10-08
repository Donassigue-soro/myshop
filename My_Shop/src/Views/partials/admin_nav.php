<?php $section = $section ?? ''; ?>
<nav class="admin-nav" aria-label="Administration">
    <a href="<?= url('/admin') ?>" class="<?= $section === 'dashboard' ? 'active' : '' ?>">Tableau de bord</a>
    <a href="<?= url('/admin/products') ?>" class="<?= $section === 'products' ? 'active' : '' ?>">Produits</a>
    <a href="<?= url('/admin/categories') ?>" class="<?= $section === 'categories' ? 'active' : '' ?>">Catégories</a>
    <a href="<?= url('/admin/users') ?>" class="<?= $section === 'users' ? 'active' : '' ?>">Utilisateurs</a>
    <a href="<?= url('/admin/orders') ?>" class="<?= $section === 'orders' ? 'active' : '' ?>">Commandes</a>
</nav>
