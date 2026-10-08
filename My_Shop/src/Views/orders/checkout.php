<main class="page">
    <h1>Validation de la commande</h1>
    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>Produit</th><th>Prix</th><th>Qté</th><th>Sous-total</th></tr></thead>
        <tbody>
        <?php foreach ($lines as $l): ?>
            <tr>
                <td><?= e($l['product']['name']) ?></td>
                <td><?= e(money($l['product']['price'])) ?></td>
                <td><?= (int)$l['qty'] ?></td>
                <td><?= e(money($l['subtotal'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot><tr><th colspan="3" class="right">Total</th><th><?= e(money($total)) ?></th></tr></tfoot>
    </table>
    </div>
    <form method="post" action="<?= url('/checkout') ?>" class="actions">
        <?= csrf_field() ?>
        <a href="<?= url('/cart') ?>" class="btn btn-outline">Modifier le panier</a>
        <button type="submit" class="btn">Confirmer la commande</button>
    </form>
    <p class="hint">Le paiement en ligne n'est pas encore branché : la commande est enregistrée avec le statut « En attente ».</p>
</main>
