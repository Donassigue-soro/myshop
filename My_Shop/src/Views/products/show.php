<main class="detail">
    <div class="detail-image cadre">
        <img src="<?= e(product_image($product['image_path'])) ?>" alt="<?= e($product['name']) ?>">
    </div>
    <div class="detail-info">
        <p class="cat"><?= e($product['category_name'] ?? 'Sans catégorie') ?></p>
        <h1><?= e($product['name']) ?></h1>
        <p class="price"><?= e(money($product['price'])) ?></p>
        <p class="description"><?= nl2br(e($product['description'] ?? '')) ?></p>

        <form method="post" action="<?= url('/cart/add') ?>" class="add-form">
            <?= csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
            <input type="hidden" name="return" value="<?= e(current_uri()) ?>">
            <label for="qty">Quantité</label>
            <input type="number" id="qty" name="qty" value="1" min="1" max="99">
            <button type="submit" class="btn">Ajouter au panier</button>
        </form>
        <p><a href="<?= url('/') ?>">← Retour à la boutique</a></p>
    </div>
</main>
