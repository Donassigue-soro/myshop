<?php
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = __DIR__ . rawurldecode($path);

if ($path !== '/' && is_file($file) && strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'php') {
    return false;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/index.php';