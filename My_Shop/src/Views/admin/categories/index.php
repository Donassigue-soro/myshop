<?php \WecodeGuy\ProjetMyShop\Core\View::partial('partials/admin_nav', ['section' => $section]); ?>
<main class="page">
    <h1>Catégories</h1>

    <form method="post" action="<?= url('/admin/categories') ?>" class="inline-search">
        <?= csrf_field() ?>
        <input type="text" name="name" placeholder="Nom de la catégorie" required maxlength="100" aria-label="Nom">
        <select name="parent_id" aria-label="Catégorie parente">
            <option value="0">— Catégorie principale —</option>
            <?php foreach ($categories as $c): if ($c['parent_id'] === null): ?>
                <option value="<?= (int)$c['id'] ?>">Sous-catégorie de : <?= e($c['name']) ?></option>
            <?php endif; endforeach; ?>
        </select>
        <button type="submit" class="btn">Ajouter</button>
    </form>

    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>Nom</th><th>Parent</th><th>Produits</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($categories as $c): ?>
            <tr>
                <td><?= $c['parent_id'] ? '— ' : '' ?><?= e($c['name']) ?></td>
                <td><?= e($c['parent_name'] ?? '—') ?></td>
                <td><?= (int)$c['product_count'] ?></td>
                <td>
                    <form method="post" action="<?= url('/admin/categories/' . (int)$c['id'] . '/delete') ?>" class="inline-form" data-confirm="Supprimer la catégorie « <?= e($c['name']) ?> » ?">
                        <?= csrf_field() ?>
                        <button type="submit" class="link-button danger">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$categories): ?><tr><td colspan="4" class="empty">Aucune catégorie.</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</main>
