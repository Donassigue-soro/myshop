-- My Shop : schéma de la base de données
-- Import : mysql -u root -p < database/schema.sql   (ou via phpMyAdmin > Importer)

CREATE DATABASE IF NOT EXISTS my_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE my_shop;

CREATE TABLE IF NOT EXISTS users (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(30)  NOT NULL,
    email      VARCHAR(190) NOT NULL,
    password   VARCHAR(255) NOT NULL,              -- hash password_hash() (bcrypt/argon)
    admin      TINYINT(1)   NOT NULL DEFAULT 0,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_users_username (username),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
    id        INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name      VARCHAR(100) NOT NULL,
    parent_id INT UNSIGNED NULL,
    CONSTRAINT fk_categories_parent FOREIGN KEY (parent_id) REFERENCES categories (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(150)  NOT NULL,
    description TEXT          NULL,
    price       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    category_id INT UNSIGNED  NULL,
    image_path  VARCHAR(255)  NULL,                -- chemin relatif à /public
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_products_category (category_id),
    KEY idx_products_price (price),
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    total      DECIMAL(10,2) NOT NULL,
    status     ENUM('pending','paid','shipped','cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_orders_user (user_id),
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Le nom et le prix sont "photographiés" au moment de la commande
CREATE TABLE IF NOT EXISTS order_items (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    order_id     INT UNSIGNED NOT NULL,
    product_id   INT UNSIGNED NULL,
    product_name VARCHAR(150) NOT NULL,
    unit_price   DECIMAL(10,2) NOT NULL,
    quantity     INT UNSIGNED NOT NULL,
    KEY idx_items_order (order_id),
    CONSTRAINT fk_items_order   FOREIGN KEY (order_id)   REFERENCES orders (id)   ON DELETE CASCADE,
    CONSTRAINT fk_items_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE SET NULL
) ENGINE=InnoDB;
