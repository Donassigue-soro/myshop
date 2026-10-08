<?php
/**
 * Création d'un compte administrateur (seule façon d'obtenir un admin).
 * Usage : php bin/create_admin.php <username> <email> [mot_de_passe]
 */
if (PHP_SAPI !== 'cli') {
    exit("Script réservé à la ligne de commande.\n");
}

define('ROOT', dirname(__DIR__));
define('PUBLIC_PATH', ROOT . '/public');
require ROOT . '/vendor/autoload.php';
require ROOT . '/src/Core/helpers.php';

use WecodeGuy\ProjetMyShop\Core\Config;
use WecodeGuy\ProjetMyShop\Models\User;

Config::load(ROOT . '/config/config.php');

[$script, $username, $email] = array_pad($argv, 3, null);
$password = $argv[3] ?? null;

if (!$username || !$email) {
    exit("Usage : php bin/create_admin.php <username> <email> [mot_de_passe]\n");
}
if (!$password) {
    echo 'Mot de passe (8 caractères min., lettres + chiffres) : ';
    $password = trim((string)fgets(STDIN));
}
if (!preg_match('/^[A-Za-z0-9_.-]{3,30}$/', $username)) {
    exit("Nom d'utilisateur invalide.\n");
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("Email invalide.\n");
}
if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
    exit("Mot de passe trop faible (8 caractères min., au moins une lettre et un chiffre).\n");
}

$users = new User();
if ($users->conflicts($username, strtolower($email))) {
    exit("Ce nom d'utilisateur ou cet email existe déjà.\n");
}
$id = $users->create($username, strtolower($email), password_hash($password, PASSWORD_DEFAULT), true);
echo "Administrateur #$id créé avec succès.\n";
