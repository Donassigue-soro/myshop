<?php
namespace WecodeGuy\ProjetMyShop\Controllers;

use WecodeGuy\ProjetMyShop\Core\Auth;
use WecodeGuy\ProjetMyShop\Models\User;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS = 5;
    private const LOCK_SECONDS = 300;

    // ------------------------------------------------------------ Connexion
    public function showSignin(): void
    {
        if (Auth::check()) {
            redirect('/');
        }
        $this->view('auth/signin', ['title' => 'Connexion', 'errors' => [], 'old' => []]);
    }

    public function signin(): void
    {
        $login = $this->post('name_email');
        $pass  = $_POST['password'] ?? '';
        $pass  = is_string($pass) ? $pass : '';

        if ($this->isLocked()) {
            http_response_code(429);
            $this->view('auth/signin', ['title' => 'Connexion', 'old' => ['name_email' => $login],
                'errors' => ['global' => 'Trop de tentatives. Réessayez dans quelques minutes.']]);
            return;
        }

        $users = new User();
        $user  = ($login !== '' && $pass !== '') ? $users->findByLogin($login) : null;

        if ($user && $this->checkPassword($user, $pass, $users)) {
            unset($_SESSION['login_attempts']);
            Auth::login((int)$user['id']);
            redirect(Auth::intended('/'));
        }

        $this->registerFailure();
        http_response_code(401);
        // Message volontairement générique : on ne révèle pas si le compte existe
        $this->view('auth/signin', ['title' => 'Connexion', 'old' => ['name_email' => $login],
            'errors' => ['global' => 'Identifiants incorrects.']]);
    }

    /** Vérifie le mot de passe, migre automatiquement les anciens hash SHA-256 vers bcrypt/argon */
    private function checkPassword(array $user, string $password, User $users): bool
    {
        $hash = $user['password'];

        if (password_verify($password, $hash)) {
            if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
                $users->updatePassword((int)$user['id'], password_hash($password, PASSWORD_DEFAULT));
            }
            return true;
        }

        // Ancien format (sha256 sans sel) : accepté UNE fois puis converti
        if (preg_match('/^[a-f0-9]{64}$/i', $hash) && hash_equals(strtolower($hash), hash('sha256', $password))) {
            $users->updatePassword((int)$user['id'], password_hash($password, PASSWORD_DEFAULT));
            return true;
        }
        return false;
    }

    private function isLocked(): bool
    {
        $a = $_SESSION['login_attempts'] ?? ['n' => 0, 't' => 0];
        if ($a['n'] >= self::MAX_ATTEMPTS && (time() - $a['t']) < self::LOCK_SECONDS) {
            return true;
        }
        if ((time() - $a['t']) >= self::LOCK_SECONDS) {
            unset($_SESSION['login_attempts']);
        }
        return false;
    }

    private function registerFailure(): void
    {
        $a = $_SESSION['login_attempts'] ?? ['n' => 0, 't' => 0];
        $_SESSION['login_attempts'] = ['n' => $a['n'] + 1, 't' => time()];
    }

    // ----------------------------------------------------------- Inscription
    public function showSignup(): void
    {
        if (Auth::check()) {
            redirect('/');
        }
        $this->view('auth/signup', ['title' => 'Inscription', 'errors' => [], 'old' => []]);
    }

    public function signup(): void
    {
        $username = $this->post('username');
        $email    = mb_strtolower($this->post('email'));
        $pass     = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
        $confirm  = is_string($_POST['password_confirmation'] ?? null) ? $_POST['password_confirmation'] : '';

        $errors = $this->validateIdentity($username, $email);
        $errors += $this->validateNewPassword($pass, $confirm);

        $users = new User();
        if (!$errors) {
            foreach ($users->conflicts($username, $email) as $field) {
                $errors[$field] = $field === 'email' ? 'Cet email est déjà utilisé.' : "Ce nom d'utilisateur est déjà pris.";
            }
        }

        if (!$errors) {
            try {
                // IMPORTANT : un compte créé par ce formulaire n'est JAMAIS administrateur
                $users->create($username, $email, password_hash($pass, PASSWORD_DEFAULT), false);
                $this->flash('success', 'Compte créé avec succès. Vous pouvez vous connecter.');
                redirect('/signin');
            } catch (\PDOException $e) {
                if ($e->getCode() !== '23000') { // 23000 = doublon (course entre deux inscriptions)
                    throw $e;
                }
                $errors['global'] = "Ce nom d'utilisateur ou cet email est déjà utilisé.";
            }
        }

        http_response_code(422);
        $this->view('auth/signup', ['title' => 'Inscription', 'errors' => $errors, 'old' => ['username' => $username, 'email' => $email]]);
    }

    // ---------------------------------------------------------------- Profil
    public function showProfile(): void
    {
        Auth::requireLogin();
        $this->view('auth/profile', ['title' => 'Mon profil', 'errors' => [], 'user' => Auth::user()]);
    }

    public function updateProfile(): void
    {
        Auth::requireLogin();
        $current  = Auth::user();
        $users    = new User();

        $username = $this->post('username');
        $email    = mb_strtolower($this->post('email'));
        $currentPw = is_string($_POST['current_password'] ?? null) ? $_POST['current_password'] : '';
        $newPw    = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
        $confirm  = is_string($_POST['password_confirmation'] ?? null) ? $_POST['password_confirmation'] : '';

        $errors = $this->validateIdentity($username, $email);
        if ($newPw !== '' || $confirm !== '') {
            $errors += $this->validateNewPassword($newPw, $confirm);
        }
        // Toute modification exige le mot de passe actuel
        if (!password_verify($currentPw, $current['password'])
            && !(preg_match('/^[a-f0-9]{64}$/i', $current['password']) && hash_equals(strtolower($current['password']), hash('sha256', $currentPw)))) {
            $errors['current_password'] = 'Mot de passe actuel incorrect.';
        }
        if (!$errors) {
            foreach ($users->conflicts($username, $email, (int)$current['id']) as $field) {
                $errors[$field] = $field === 'email' ? 'Cet email est déjà utilisé.' : "Ce nom d'utilisateur est déjà pris.";
            }
        }

        if ($errors) {
            http_response_code(422);
            $this->view('auth/profile', ['title' => 'Mon profil', 'errors' => $errors,
                'user' => ['username' => $username, 'email' => $email] + $current]);
            return;
        }

        $users->updateProfile((int)$current['id'], $username, $email);
        if ($newPw !== '') {
            $users->updatePassword((int)$current['id'], password_hash($newPw, PASSWORD_DEFAULT));
        }
        $this->flash('success', 'Profil mis à jour.');
        redirect('/profile');
    }

    // ------------------------------------------------------------ Déconnexion
    public function logout(): void
    {
        Auth::logout();
        $this->flash('success', 'Vous êtes déconnecté.');
        redirect('/');
    }

    // ------------------------------------------------------------ Validation
    private function validateIdentity(string $username, string $email): array
    {
        $errors = [];
        if (!preg_match('/^[A-Za-z0-9_.-]{3,30}$/', $username)) {
            $errors['username'] = '3 à 30 caractères : lettres, chiffres, point, tiret ou underscore.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190) {
            $errors['email'] = 'Adresse email invalide.';
        }
        return $errors;
    }

    private function validateNewPassword(string $pass, string $confirm): array
    {
        $errors = [];
        if (strlen($pass) < 8 || strlen($pass) > 72) {
            $errors['password'] = 'Le mot de passe doit contenir entre 8 et 72 caractères.';
        } elseif (!preg_match('/[A-Za-z]/', $pass) || !preg_match('/\d/', $pass)) {
            $errors['password'] = 'Le mot de passe doit contenir au moins une lettre et un chiffre.';
        }
        if ($pass !== $confirm) {
            $errors['paswd_conf'] = 'La confirmation ne correspond pas.';
        }
        return $errors;
    }
}
