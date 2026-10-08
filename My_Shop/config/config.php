<?php
/**
 * Configuration de l'application.
 * Ne mettez JAMAIS vos vrais identifiants ici : créez config/config.local.php
 * (ignoré par git) ou utilisez des variables d'environnement.
 */
$config = [
    'env'      => getenv('APP_ENV') ?: 'dev',          // 'dev' ou 'prod'
    'currency' => '€',
    'per_page' => 8,                                    // produits par page
    'upload_max_bytes' => 2 * 1024 * 1024,              // 2 Mo
    'db' => [
        'host'    => getenv('DB_HOST') ?: '127.0.0.1',
        'port'    => getenv('DB_PORT') ?: '3306',
        'name'    => getenv('DB_NAME') ?: 'my_shop',
        'user'    => getenv('DB_USER') ?: 'root',
        'pass'    => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
        'charset' => 'utf8mb4',
    ],
];

$local = __DIR__ . '/config.local.php';
if (is_file($local)) {
    $config = array_replace_recursive($config, require $local);
}

return $config;
