<?php
namespace WecodeGuy\ProjetMyShop\Core;

class Router
{
    private array $routes = [];

    public function get(string $pattern, array $handler): void  { $this->add('GET', $pattern, $handler); }
    public function post(string $pattern, array $handler): void { $this->add('POST', $pattern, $handler); }

    private function add(string $method, string $pattern, array $handler): void
    {
        $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern) . '$#';
        $this->routes[] = [$method, $regex, $handler];
    }

    /** Chemin demandé, relatif à l'application (indépendant du sous-dossier d'installation) */
    public static function currentPath(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $path = rawurldecode($path);

        $base = base_path();                                   // ex. /My_Shop/public
        $root = rtrim(str_replace('\\', '/', dirname($base)), '/'); // ex. /My_Shop
        if ($root === '.') {
            $root = '';
        }

        foreach ([$base, $root] as $prefix) {
            if ($prefix === '') {
                continue;
            }
            if ($path === $prefix) {
                return '/';
            }
            if (str_starts_with($path, $prefix . '/')) {
                $path = substr($path, strlen($prefix));
                break;
            }
        }
        return '/' . trim($path, '/');
    }

    public function dispatch(string $method, string $path): void
    {
        try {
            $pathMatched = false;

            foreach ($this->routes as [$routeMethod, $regex, $handler]) {
                if (!preg_match($regex, $path, $matches)) {
                    continue;
                }
                $pathMatched = true;
                if ($routeMethod !== $method) {
                    continue;
                }

                // Protection CSRF centralisée pour toutes les requêtes POST
                if ($method === 'POST' && !Csrf::verify()) {
                    $this->error(419, 'Session expirée ou jeton de sécurité invalide. Rechargez la page et réessayez.');
                    return;
                }

                $params = array_values(array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY));
                [$class, $action] = $handler;
                $controller = new $class();
                $controller->$action(...$params);
                return;
            }

            if ($pathMatched) {
                $this->error(405, 'Méthode non autorisée.');
            } else {
                $this->error(404, 'La page demandée est introuvable.');
            }
        } catch (\Throwable $e) {
            Logger::error(get_class($e) . ': ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
            $message = Config::get('env') === 'dev'
                ? $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')'
                : 'Une erreur est survenue. Réessayez plus tard.';
            $this->error(500, $message);
        }
    }

    private function error(int $code, string $message): void
    {
        http_response_code($code);
        try {
            View::render('errors/error', ['code' => $code, 'message' => $message, 'title' => 'Erreur ' . $code]);
        } catch (\Throwable $e) {
            // Dernier recours (ex. base de données hors service pendant le rendu du layout)
            echo '<!DOCTYPE html><meta charset="utf-8"><title>Erreur ' . $code . '</title><h1>Erreur ' . $code . '</h1><p>'
                . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
        }
    }
}
