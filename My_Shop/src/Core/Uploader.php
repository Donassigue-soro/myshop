<?php
namespace WecodeGuy\ProjetMyShop\Core;

/** Upload sécurisé d'images : l'extension est déduite du CONTENU, jamais du nom envoyé par le client */
class Uploader
{
    private const TYPES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];
    private const DIR = 'uploads/products/';

    /**
     * @return string|null chemin relatif à /public, ou null (aucun fichier OU erreur → voir $error)
     */
    public static function image(?array $file, ?string &$error = null): ?string
    {
        $error = null;
        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error = ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE)
                ? "L'image est trop volumineuse."
                : "Erreur lors de l'envoi de l'image.";
            return null;
        }
        if ($file['size'] > Config::get('upload_max_bytes')) {
            $error = "L'image dépasse " . round(Config::get('upload_max_bytes') / 1048576) . ' Mo.';
            return null;
        }
        if (!is_uploaded_file($file['tmp_name'])) {
            $error = 'Fichier invalide.';
            return null;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!isset(self::TYPES[$mime]) || @getimagesize($file['tmp_name']) === false) {
            $error = 'Format non autorisé (JPEG, PNG, GIF ou WebP uniquement).';
            return null;
        }

        $dir = PUBLIC_PATH . '/' . self::DIR;
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            $error = "Impossible de créer le dossier d'upload.";
            return null;
        }

        $name = bin2hex(random_bytes(16)) . '.' . self::TYPES[$mime];
        if (!move_uploaded_file($file['tmp_name'], $dir . $name)) {
            $error = "Impossible d'enregistrer l'image.";
            return null;
        }
        @chmod($dir . $name, 0644);
        return self::DIR . $name;
    }

    /** Supprime un fichier uploadé (les images de démo dans /assets ne sont jamais touchées) */
    public static function delete(?string $path): void
    {
        if ($path && str_starts_with($path, self::DIR) && basename($path) === substr($path, strlen(self::DIR))) {
            $full = PUBLIC_PATH . '/' . $path;
            if (is_file($full)) {
                @unlink($full);
            }
        }
    }
}
