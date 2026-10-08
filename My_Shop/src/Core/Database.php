<?php
namespace WecodeGuy\ProjetMyShop\Core;

use PDO;
use PDOException;

/** Connexion PDO unique (singleton) */
class Database
{
    private static ?PDO $pdo = null;

    public static function get(): PDO
    {
        if (self::$pdo === null) {
            $c = Config::get('db');
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $c['host'], $c['port'], $c['name'], $c['charset']);
            try {
                self::$pdo = new PDO($dsn, $c['user'], $c['pass'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                Logger::error('Connexion BDD impossible : ' . $e->getMessage());
                throw new \RuntimeException('Connexion à la base de données impossible.');
            }
        }
        return self::$pdo;
    }
}
