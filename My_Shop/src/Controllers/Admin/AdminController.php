<?php
namespace WecodeGuy\ProjetMyShop\Controllers\Admin;

use WecodeGuy\ProjetMyShop\Controllers\Controller;
use WecodeGuy\ProjetMyShop\Core\Auth;

/** Base de tous les contrôleurs d'administration : accès réservé aux admins (vérifié en base) */
abstract class AdminController extends Controller
{
    public function __construct()
    {
        Auth::requireAdmin();
    }
}
