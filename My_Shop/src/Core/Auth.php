<?php
namespace WecodeGuy\ProjetMyShop\Core;

use WecodeGuy\ProjetMyShop\Models\User;

class Auth
{
    private static ?array $user = null;
    private static bool $loaded = false;

    public static function id(): ?int
    {
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }

    /** Utilisateur courant, relu en base (le statut admin n'est jamais "caché" en session) */
    public static function user(): ?array
    {
        if (!self::$loaded) {
            self::$loaded = true;
            $id = self::id();
            if ($id !== null) {
                self::$user = (new User())->find($id);
                if (self::$user === null) {
                    unset($_SESSION['user_id']);
                }
            }
        }
        return self::$user;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function isAdmin(): bool
    {
        $user = self::user();
        return $user !== null && (int)$user['admin'] === 1;
    }

    public static function login(int $userId): void
    {
        session_regenerate_id(true);          // anti fixation de session
        unset($_SESSION['_csrf']);
        $_SESSION['user_id'] = $userId;
        self::$loaded = false;
        self::$user = null;
    }

    public static function logout(): void
    {
        unset($_SESSION['user_id'], $_SESSION['_csrf']);
        session_regenerate_id(true);
        self::$loaded = false;
        self::$user = null;
    }

    public static function requireLogin(): void
    {
        if (self::check()) {
            return;
        }
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
            $_SESSION['redirect_url'] = current_uri();
        }
        Session::flash('error', 'Veuillez vous connecter pour continuer.');
        redirect('/signin');
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            http_response_code(403);
            View::render('errors/error', ['code' => 403, 'message' => "Accès réservé aux administrateurs.", 'title' => 'Accès refusé']);
            exit;
        }
    }

    /** Page demandée avant la connexion (toujours un chemin interne) */
    public static function intended(string $default = '/'): string
    {
        $target = safe_path($_SESSION['redirect_url'] ?? null, $default);
        unset($_SESSION['redirect_url']);
        return $target;
    }
}
