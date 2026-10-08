<main class="auth">
    <form class="cadre auth-card" action="<?= url('/signin') ?>" method="post" novalidate>
        <?= csrf_field() ?>
        <h1 class="titre">Connexion</h1>

        <?php if (!empty($errors['global'])): ?>
            <p class="errors" role="alert"><?= e($errors['global']) ?></p>
        <?php endif; ?>

        <div>
            <label for="nom">Nom d'utilisateur ou email</label>
            <input type="text" id="nom" name="name_email" value="<?= e($old['name_email'] ?? '') ?>" autocomplete="username" required autofocus>
        </div>
        <div>
            <label for="mdp">Mot de passe</label>
            <input type="password" id="mdp" name="password" autocomplete="current-password" required>
        </div>
        <button type="submit" class="btn btn-block">Se connecter</button>
        <p class="titre">Pas de compte ? <a href="<?= url('/signup') ?>">Créer un compte</a></p>
    </form>
</main>
