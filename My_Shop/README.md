# My Shop — PHP natif (MVC)

Mini e-commerce : catalogue (recherche, filtres, tri, pagination), fiche produit, panier, commandes,
comptes clients et back-office (produits, catégories, utilisateurs, commandes).

## Prérequis
PHP ≥ 8.0 (extensions `pdo_mysql`, `fileinfo`), MySQL/MariaDB, Apache avec `mod_rewrite`.

## Installation
1. Copiez le dossier dans `htdocs/` (XAMPP) ou `www/` (WAMP).
2. Importez la base : `database/schema.sql`, puis (optionnel) `database/seed.sql` pour des produits de démo.
3. Configurez la connexion : copiez `config/config.local.example.php` en `config/config.local.php`
   et renseignez vos identifiants (ce fichier n'est jamais commité).
4. Créez votre administrateur (seule façon d'en obtenir un) :
   ```
   php bin/create_admin.php admin admin@exemple.com
   ```
5. Ouvrez `http://localhost/My_Shop/` (le `.htaccess` racine redirige vers `/public`).
   En production, faites pointer le DocumentRoot **directement sur `public/`** et mettez `APP_ENV=prod`.

## Structure
```
config/      configuration + routes
database/    schema.sql, seed.sql
bin/         scripts CLI (create_admin.php)
public/      SEUL dossier exposé : index.php, assets/, uploads/
src/Core/    Router, Auth, Csrf, Session, Database, Cart, Uploader, View...
src/Models/  User, Product, Category, Order (PDO, requêtes préparées)
src/Controllers/ (+ Admin/)
src/Views/   templates PHP
storage/logs journaux (hors web)
```

## Sécurité mise en place
- Mots de passe : `password_hash` / `password_verify` (les anciens hash SHA-256 sont migrés à la 1re connexion).
- Inscription : plus de case « admin ». Droits admin = script CLI ou page Utilisateurs (admin uniquement).
- CSRF vérifié sur **toutes** les requêtes POST ; `session_regenerate_id` à la connexion ; cookies HttpOnly/SameSite.
- Upload : extension déduite du contenu (finfo + getimagesize), nom aléatoire, exécution PHP interdite dans `uploads/`.
- Sorties échappées (`e()`), requêtes préparées partout, CSP + en-têtes de sécurité.
- `src/`, `config/`, `vendor/`, `storage/` ne sont pas accessibles par le navigateur.
- Erreurs affichées seulement en `dev`, toujours journalisées dans `storage/logs/`.
- Un admin ne peut pas modifier ses propres droits. Un client ne voit que ses commandes.

## Limites connues / pistes
- Le paiement n'est pas branché (commande enregistrée « En attente »).
- Anti-brute-force basé sur la session (basique) : ajouter une limitation par IP/compte en production.
- Pas de gestion de stock ni d'envoi d'emails.
