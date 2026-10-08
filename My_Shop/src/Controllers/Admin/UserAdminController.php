<?php
namespace WecodeGuy\ProjetMyShop\Controllers\Admin;

use WecodeGuy\ProjetMyShop\Core\Auth;
use WecodeGuy\ProjetMyShop\Models\User;

class UserAdminController extends AdminController
{
    public function index(): void
    {
        $this->view('admin/users/index', [
            'title' => 'Utilisateurs', 'section' => 'users',
            'users' => (new User())->all(), 'currentId' => Auth::id(),
        ]);
    }

    public function toggleAdmin(string $id): void
    {
        $id = (int)$id;
        $users = new User();
        $target = $users->find($id);

        if (!$target) {
            $this->flash('error', 'Utilisateur introuvable.');
        } elseif ($id === Auth::id()) {
            // Empêche un admin de se retirer ses propres droits (et de verrouiller l'administration)
            $this->flash('error', 'Vous ne pouvez pas modifier vos propres droits.');
        } else {
            $makeAdmin = (int)$target['admin'] !== 1;
            $users->setAdmin($id, $makeAdmin);
            $this->flash('success', $target['username'] . ($makeAdmin ? ' est maintenant administrateur.' : " n'est plus administrateur."));
        }
        redirect('/admin/users');   // Post/Redirect/Get : la liste affichée est à jour
    }
}
