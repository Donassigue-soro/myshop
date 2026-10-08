<article class="cadre card">
    <a href="<?= url('/product/' . (int)$product['id']) ?>">
        <img src="<?= e(product_image($product['image_path'])) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
    </a>
    <div class="info">
        <div class="info1">
            <h3><a href="<?= url('/product/' . (int)$product['id']) ?>"><?= e($product['name']) ?></a></h3>
            <span><?= e(money($product['price'])) ?></span>
        </div>
        <p class="cat"><?= e($product['category_name'] ?? 'Sans catégorie') ?></p>
        <div class="info1">
            <span></span>
            <form method="post" action="<?= url('/cart/add') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                <input type="hidden" name="return" value="<?= e(current_uri()) ?>">
                <button type="submit" class="icon-button" aria-label="Ajouter <?= e($product['name']) ?> au panier">
                    <img src="<?= asset('design/cart.png') ?>" alt="" class="panier">
                </button>
            </form>
        </div>
    </div>
</article>
