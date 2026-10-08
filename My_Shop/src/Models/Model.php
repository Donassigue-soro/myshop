<?php
namespace WecodeGuy\ProjetMyShop\Models;

use PDO;
use WecodeGuy\ProjetMyShop\Core\Database;

abstract class Model
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::get();
    }
}
