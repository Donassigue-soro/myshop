<?php use WecodeGuy\ProjetMyShop\Core\View; ?>
<!-- Formulaire GET unique : les champs (hors du formulaire) y sont rattachés via l'attribut form="filters" -->
<form id="filters" method="get" action="<?= url('/') ?>"></form>

<section class="recherche">
    <div class="living">
        <img src="<?= asset('design/Search.png') ?>" alt="" class="icon">
        <input type="search" name="q" form="filters" placeholder="Rechercher un produit…" value="<?= e($filters['q']) ?>" aria-label="Recherche" maxlength="100">
        <button type="submit" form="filters" class="btn">Rechercher</button>
    </div>
    <div>
        <select name="sort" form="filters" class="best" aria-label="Trier par" data-autosubmit>
            <?php foreach ($sorts as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= $filters['sort'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</section>

<main class="conteneur">
    <div class="cadre filter-box">
        <aside>
            <h4>FILTRER PAR</h4>
            <label for="category">Catégorie</label>
            <select id="category" name="category" form="filters" class="option">
                <option value="0">Toutes</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= (int)$c['id'] ?>" <?= (int)$filters['category'] === (int)$c['id'] ? 'selected' : '' ?>>
                        <?= $c['parent_id'] ? '— ' : '' ?><?= e($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="filter-title">Fourchette de prix</div>
            <div class="price-range">
                <input type="number" name="min" form="filters" min="0" step="1" placeholder="Min" value="<?= e($filters['min'] ?? '') ?>" aria-label="Prix minimum">
                <input type="number" name="max" form="filters" min="0" step="1" placeholder="Max" value="<?= e($filters['max'] ?? '') ?>" aria-label="Prix maximum">
            </div>
            <button type="submit" form="filters" class="btn btn-block">Appliquer</button>
            <a href="<?= url('/') ?>" class="reset">Réinitialiser</a>
        </aside>
    </div>

    <?php if (!$products): ?>
        <p class="empty">Aucun produit ne correspond à votre recherche.</p>
    <?php endif; ?>

    <?php foreach ($products as $product): ?>
        <?php View::partial('partials/product_card', ['product' => $product]); ?>
    <?php endforeach; ?>
</main>

<p class="result-count"><?= (int)$total ?> produit<?= $total > 1 ? 's' : '' ?></p>
<?php View::partial('partials/pagination', ['page' => $page, 'pages' => $pages, 'basePath' => '/']); ?>
