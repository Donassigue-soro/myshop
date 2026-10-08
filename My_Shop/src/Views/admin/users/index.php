<?php \WecodeGuy\ProjetMyShop\Core\View::partial('partials/admin_nav', ['section' => $section]); ?>
<main class="page">
    <h1>Utilisateurs</h1>
    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= (int)$u['id'] ?></td>
                <td><?= e($u['username']) ?></td>
                <td><?= e($u['email']) ?></td>
                <td><?= (int)$u['admin'] === 1 ? 'Administrateur' : 'Client' ?></td>
                <td>
                    <?php if ((int)$u['id'] === (int)$currentId): ?>
                        <em>(vous)</em>
                    <?php else: ?>
                        <form method="post" action="<?= url('/admin/users/' . (int)$u['id'] . '/toggle-admin') ?>" class="inline-form"
                              data-confirm="<?= (int)$u['admin'] === 1 ? 'Retirer les droits admin de ' : 'Donner les droits admin à ' ?><?= e($u['username']) ?> ?">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-small btn-outline"><?= (int)$u['admin'] === 1 ? 'Retirer admin' : 'Donner admin' ?></button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</main>
