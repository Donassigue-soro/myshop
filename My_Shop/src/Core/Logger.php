<?php
namespace WecodeGuy\ProjetMyShop\Core;

class Logger
{
    public static function error(string $message): void
    {
        $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
        @error_log($line, 3, ROOT . '/storage/logs/app.log');
    }
}
