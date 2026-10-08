<main class="page">
    <h1>Mes commandes</h1>
    <?php if (!$orders): ?>
        <p class="empty">Vous n'avez pas encore passé de commande.</p>
    <?php else: ?>
        <div class="table-wrap">
        <table class="table">
            <thead><tr><th>N°</th><th>Date</th><th>Statut</th><th>Total</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td>#<?= (int)$o['id'] ?></td>
                    <td><?= e(date('d/m/Y H:i', strtotime($o['created_at']))) ?></td>
                    <td><span class="status status-<?= e($o['status']) ?>"><?= e($statuses[$o['status']] ?? $o['status']) ?></span></td>
                    <td><?= e(money($o['total'])) ?></td>
                    <td><a href="<?= url('/orders/' . (int)$o['id']) ?>">Détails</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</main>
