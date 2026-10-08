<?php
namespace WecodeGuy\ProjetMyShop\Core;

class View
{
    /** Rend une vue dans un layout (null = sans layout) */
    public static function render(string $view, array $data = [], ?string $layout = 'layout/main'): void
    {
        $content = self::capture($view, $data);
        if ($layout === null) {
            echo $content;
            return;
        }
        $data['content'] = $content;
        self::load($layout, $data);
    }

    /** Affiche un fragment de vue (partial) */
    public static function partial(string $view, array $data = []): void
    {
        self::load($view, $data);
    }

    private static function capture(string $view, array $data): string
    {
        ob_start();
        self::load($view, $data);
        return (string)ob_get_clean();
    }

    private static function load(string $__view, array $__data): void
    {
        extract($__data, EXTR_SKIP);
        require ROOT . '/src/Views/' . $__view . '.php';
    }
}
