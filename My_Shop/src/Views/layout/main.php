<?php
use WecodeGuy\ProjetMyShop\Core\{Auth, Cart, Session};
$user = Auth::user();
$cartCount = Cart::count();
$flashes = Session::pullFlash();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Accueil') ?> · My Shop</title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body>
<header class="header">
    <div class="navbar">
        <a href="<?= url('/') ?>" class="logo-link"><img src="<?= asset('design/Logo.png') ?>" alt="My Shop" class="logo"></a>
        <button class="menu-toggle" id="menu-toggle" type="button" aria-expanded="false" aria-controls="main-nav" aria-label="Menu">☰</button>
    </div>
    <nav class="nav" id="main-nav">
        <a href="<?= url('/') ?>">BOUTIQUE</a>
        <?php if ($user): ?>
            <a href="<?= url('/orders') ?>">MES COMMANDES</a>
            <a href="<?= url('/profile') ?>"><?= e($user['username']) ?></a>
            <?php if (Auth::isAdmin()): ?>
                <a href="<?= url('/admin') ?>">ADMIN</a>
            <?php endif; ?>
        <?php endif; ?>
        <a href="<?= url('/cart') ?>" class="cart-link" aria-label="Panier">
            <img src="<?= asset('design/cart.png') ?>" alt="" class="panier">
            <?php if ($cartCount > 0): ?><span class="badge"><?= (int)$cartCount ?></span><?php endif; ?>
        </a>
        <?php if ($user): ?>
            <form method="post" action="<?= url('/logout') ?>" class="inline-form">
                <?= csrf_field() ?>
                <button type="submit" class="link-button">LOGOUT</button>
            </form>
        <?php else: ?>
            <a href="<?= url('/signin') ?>" class="login">LOGIN</a>
        <?php endif; ?>
    </nav>
</header>

<?php if ($flashes): ?>
    <div class="flashes" role="status">
        <?php foreach ($flashes as $f): ?>
            <p class="flash flash-<?= e($f['type']) ?>"><?= e($f['message']) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $content ?>

<footer class="footer">© <?= date('Y') ?> My Shop</footer>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
