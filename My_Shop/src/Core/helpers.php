<?php
use WecodeGuy\ProjetMyShop\Core\{Config, Router, Csrf};

/** Échappement HTML (anti-XSS) */
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Dossier public vu depuis le navigateur ('' si DocumentRoot = /public) */
function base_path(): string
{
    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    return ($dir === '/' || $dir === '.' || $dir === '') ? '' : rtrim($dir, '/');
}

function url(string $path = ''): string
{
    return base_path() . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/** URL d'une image produit (chemin relatif à /public stocké en BDD) */
function product_image(?string $path): string
{
    return $path ? url($path) : asset('design/placeholder.svg');
}

function money($amount): string
{
    return number_format((float)$amount, 2, ',', ' ') . ' ' . Config::get('currency', '€');
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(Csrf::token()) . '">';
}

/** Chemin + query de la page courante (relatif à l'application) */
function current_uri(): string
{
    $qs = $_SERVER['QUERY_STRING'] ?? '';
    return Router::currentPath() . ($qs !== '' ? '?' . $qs : '');
}

/** N'accepte qu'un chemin interne (évite les "open redirect") */
function safe_path($path, string $fallback = '/'): string
{
    if (!is_string($path) || $path === '' || $path[0] !== '/' || str_starts_with($path, '//')
        || preg_match('#[\\\\\x00-\x1F\x7F]#', $path)) {
        return $fallback;
    }
    return $path;
}

function redirect(string $path): void
{
    header('Location: ' . url(safe_path($path)));
    exit;
}

function old(array $source, string $key, $default = '')
{
    return $source[$key] ?? $default;
}
