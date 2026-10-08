<?php
define('ROOT', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);

require ROOT . '/vendor/autoload.php';
require ROOT . '/src/Core/helpers.php';

use WecodeGuy\ProjetMyShop\Core\{Config, Session, Router};

Config::load(ROOT . '/config/config.php');

// --- Gestion des erreurs : visibles en dev uniquement, toujours journalisées ---
error_reporting(E_ALL);
ini_set('display_errors', Config::get('env') === 'dev' ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', ROOT . '/storage/logs/php-error.log');

// --- En-têtes de sécurité ---
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: same-origin');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self'; script-src 'self'; form-action 'self'; frame-ancestors 'self'");

Session::start();

$router = new Router();
require ROOT . '/config/routes.php';
$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', Router::currentPath());
