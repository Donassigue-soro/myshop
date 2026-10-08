<main class="page">
    <h1>Mon panier</h1>

    <?php if (!$lines): ?>
        <p class="empty">Votre panier est vide.</p>
        <p><a href="<?= url('/') ?>" class="btn">Continuer mes achats</a></p>
    <?php else: ?>
        <form method="post" action="<?= url('/cart/update') ?>">
            <?= csrf_field() ?>
            <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Produit</th><th>Prix</th><th>Quantité</th><th>Sous-total</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($lines as $l): $p = $l['product']; ?>
                    <tr>
                        <td class="product-cell">
                            <img src="<?= e(product_image($p['image_path'])) ?>" alt="" class="thumb">
                            <a href="<?= url('/product/' . (int)$p['id']) ?>"><?= e($p['name']) ?></a>
                        </td>
                        <td><?= e(money($p['price'])) ?></td>
                        <td><input type="number" name="qty[<?= (int)$p['id'] ?>]" value="<?= (int)$l['qty'] ?>" min="0" max="99" class="qty" aria-label="Quantité de <?= e($p['name']) ?>"></td>
                        <td><?= e(money($l['subtotal'])) ?></td>
                        <td>
                            <button type="submit" class="link-button danger" formaction="<?= url('/cart/remove') ?>" name="product_id" value="<?= (int)$p['id'] ?>">Retirer</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot><tr><th colspan="3" class="right">Total</th><th><?= e(money($total)) ?></th><th></th></tr></tfoot>
            </table>
            </div>
            <div class="actions">
                <button type="submit" class="btn btn-outline">Mettre à jour</button>
                <a href="<?= url('/checkout') ?>" class="btn">Commander</a>
            </div>
        </form>
    <?php endif; ?>
</main>
