<main class="auth">
    <form class="cadre auth-card" action="<?= url('/signup') ?>" method="post" novalidate>
        <?= csrf_field() ?>
        <h1 class="titre">Inscription</h1>

        <?php if (!empty($errors['global'])): ?>
            <p class="errors" role="alert"><?= e($errors['global']) ?></p>
        <?php endif; ?>

        <div>
            <label for="nom">Nom d'utilisateur</label>
            <input type="text" id="nom" name="username" value="<?= e($old['username'] ?? '') ?>" autocomplete="username" required maxlength="30">
            <span class="errors"><?= e($errors['username'] ?? '') ?></span>
        </div>
        <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($old['email'] ?? '') ?>" autocomplete="email" required maxlength="190">
            <span class="errors"><?= e($errors['email'] ?? '') ?></span>
        </div>
        <div>
            <label for="mdp">Mot de passe</label>
            <input type="password" id="mdp" name="password" autocomplete="new-password" required minlength="8" maxlength="72">
            <span class="errors"><?= e($errors['password'] ?? '') ?></span>
        </div>
        <div>
            <label for="mdp_conf">Confirmation du mot de passe</label>
            <input type="password" id="mdp_conf" name="password_confirmation" autocomplete="new-password" required>
            <span class="errors"><?= e($errors['paswd_conf'] ?? '') ?></span>
        </div>
        <button type="submit" class="btn btn-block">Créer mon compte</button>
        <p class="titre">Déjà un compte ? <a href="<?= url('/signin') ?>">Connectez-vous ici</a></p>
    </form>
</main>
