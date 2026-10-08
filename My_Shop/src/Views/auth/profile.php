<main class="auth">
    <form class="cadre auth-card" action="<?= url('/profile') ?>" method="post" novalidate>
        <?= csrf_field() ?>
        <h1 class="titre">Mon profil</h1>

        <div>
            <label for="nom">Nom d'utilisateur</label>
            <input type="text" id="nom" name="username" value="<?= e($user['username']) ?>" required maxlength="30">
            <span class="errors"><?= e($errors['username'] ?? '') ?></span>
        </div>
        <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($user['email']) ?>" required maxlength="190">
            <span class="errors"><?= e($errors['email'] ?? '') ?></span>
        </div>
        <div>
            <label for="mdp">Nouveau mot de passe <small>(laisser vide pour ne pas changer)</small></label>
            <input type="password" id="mdp" name="password" autocomplete="new-password" maxlength="72">
            <span class="errors"><?= e($errors['password'] ?? '') ?></span>
        </div>
        <div>
            <label for="mdp_conf">Confirmation</label>
            <input type="password" id="mdp_conf" name="password_confirmation" autocomplete="new-password">
            <span class="errors"><?= e($errors['paswd_conf'] ?? '') ?></span>
        </div>
        <div>
            <label for="current">Mot de passe actuel <small>(obligatoire pour enregistrer)</small></label>
            <input type="password" id="current" name="current_password" autocomplete="current-password" required>
            <span class="errors"><?= e($errors['current_password'] ?? '') ?></span>
        </div>
        <button type="submit" class="btn btn-block">Enregistrer</button>
    </form>
</main>
