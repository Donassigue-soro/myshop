<?php \WecodeGuy\ProjetMyShop\Core\View::partial('partials/admin_nav', ['section' => $section]); ?>
<main class="page">
    <h1>Commandes</h1>
    <?php if (!$orders): ?>
        <p class="empty">Aucune commande.</p>
    <?php else: ?>
    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>N°</th><th>Client</th><th>Date</th><th>Statut</th><th>Total</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
            <tr>
                <td>#<?= (int)$o['id'] ?></td>
                <td><?= e($o['username']) ?></td>
                <td><?= e(date('d/m/Y H:i', strtotime($o['created_at']))) ?></td>
                <td><span class="status status-<?= e($o['status']) ?>"><?= e($statuses[$o['status']] ?? $o['status']) ?></span></td>
                <td><?= e(money($o['total'])) ?></td>
                <td><a href="<?= url('/admin/orders/' . (int)$o['id']) ?>">Voir / modifier</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</main>
