<?php
namespace WecodeGuy\ProjetMyShop\Core;

class Config
{
    private static array $data = [];

    public static function load(string $file): void
    {
        self::$data = require $file;
    }

    /** Lecture avec notation pointée : Config::get('db.host') */
    public static function get(string $key, $default = null)
    {
        $value = self::$data;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }
        return $value;
    }
}
