<?php
namespace WecodeGuy\ProjetMyShop\Controllers;

use WecodeGuy\ProjetMyShop\Core\View;
use WecodeGuy\ProjetMyShop\Core\Session;

abstract class Controller
{
    protected function view(string $view, array $data = [], ?string $layout = 'layout/main'): void
    {
        View::render($view, $data, $layout);
    }

    protected function notFound(string $message = 'Ressource introuvable.'): void
    {
        http_response_code(404);
        $this->view('errors/error', ['code' => 404, 'message' => $message, 'title' => 'Introuvable']);
    }

    protected function flash(string $type, string $message): void
    {
        Session::flash($type, $message);
    }

    /** Valeur texte d'un champ POST (toujours une chaîne, jamais un tableau) */
    protected function post(string $key, string $default = ''): string
    {
        $v = $_POST[$key] ?? $default;
        return is_string($v) ? trim($v) : $default;
    }

    /** Valeur texte d'un paramètre GET */
    protected function query(string $key, string $default = ''): string
    {
        $v = $_GET[$key] ?? $default;
        return is_string($v) ? trim($v) : $default;
    }

    /** Nombre décimal positif ou null si vide/invalide */
    protected function decimal(string $raw): ?float
    {
        $raw = str_replace(',', '.', $raw);
        return ($raw !== '' && is_numeric($raw) && (float)$raw >= 0) ? (float)$raw : null;
    }
}
