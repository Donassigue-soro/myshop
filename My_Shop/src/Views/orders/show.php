<?php if (!empty($adminMode)) { \WecodeGuy\ProjetMyShop\Core\View::partial('partials/admin_nav', ['section' => 'orders']); } ?>
<main class="page">
    <h1>Commande #<?= (int)$order['id'] ?></h1>
    <p>
        Passée le <?= e(date('d/m/Y à H:i', strtotime($order['created_at']))) ?>
        <?php if (!empty($adminMode)): ?> par <strong><?= e($order['username']) ?></strong> (<?= e($order['email']) ?>)<?php endif; ?>
        — <span class="status status-<?= e($order['status']) ?>"><?= e($statuses[$order['status']] ?? $order['status']) ?></span>
    </p>

    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>Produit</th><th>Prix unitaire</th><th>Qté</th><th>Sous-total</th></tr></thead>
        <tbody>
        <?php foreach ($items as $i): ?>
            <tr>
                <td><?= e($i['product_name']) ?></td>
                <td><?= e(money($i['unit_price'])) ?></td>
                <td><?= (int)$i['quantity'] ?></td>
                <td><?= e(money($i['unit_price'] * $i['quantity'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot><tr><th colspan="3" class="right">Total</th><th><?= e(money($order['total'])) ?></th></tr></tfoot>
    </table>
    </div>

    <?php if (!empty($adminMode)): ?>
        <form method="post" action="<?= url('/admin/orders/' . (int)$order['id'] . '/status') ?>" class="actions">
            <?= csrf_field() ?>
            <label for="status">Changer le statut</label>
            <select name="status" id="status">
                <?php foreach ($statuses as $key => $label): ?>
                    <option value="<?= e($key) ?>" <?= $order['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn">Enregistrer</button>
        </form>
    <?php endif; ?>
    <p><a href="<?= url($backUrl) ?>">← Retour</a></p>
</main>
