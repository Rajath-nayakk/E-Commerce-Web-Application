-- ============================================================================
-- E-Commerce Web Application - Database Schema (MySQL 8.x / MariaDB)
-- Clean, well-commented schema with foreign keys, constraints, and indexes
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `ecommerce_db` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `ecommerce_db`;

-- Drop existing tables in reverse dependency order (safe for re-runs)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `password_resets`;
DROP TABLE IF EXISTS `order_status_history`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `cart_items`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------------------
-- 1. Users Table: authentication, customer & administrator profiles
-- ----------------------------------------------------------------------------
CREATE TABLE `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. Categories Table: product grouping and catalog navigation
-- ----------------------------------------------------------------------------
CREATE TABLE `categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    INDEX `idx_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. Products Table: inventory items, pricing, images, and category relation
-- ----------------------------------------------------------------------------
CREATE TABLE `products` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `slug` VARCHAR(150) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    `original_price` DECIMAL(10, 2) NULL,
    `brand` VARCHAR(100) NULL,
    `rating` DECIMAL(2, 1) NOT NULL DEFAULT 4.5,
    `review_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `is_deal` TINYINT(1) NOT NULL DEFAULT 0,
    `stock` INT NOT NULL DEFAULT 0,
    `image` VARCHAR(255) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_products_category` 
        FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_products_category` (`category_id`),
    INDEX `idx_products_is_active` (`is_active`),
    INDEX `idx_products_slug` (`slug`),
    INDEX `idx_products_featured` (`is_featured`),
    INDEX `idx_products_deal` (`is_deal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4. Cart Items Table: persistent shopping cart for registered users
-- ----------------------------------------------------------------------------
CREATE TABLE `cart_items` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
    CONSTRAINT `fk_cart_user` 
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_cart_product` 
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY `unique_user_product` (`user_id`, `product_id`),
    INDEX `idx_cart_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 5. Orders Table: checkout records, shipping information, and order total
-- ----------------------------------------------------------------------------
CREATE TABLE `orders` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `total` DECIMAL(10, 2) NOT NULL,
    `status` ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
    `payment_method` VARCHAR(50) NOT NULL,
    `shipping_name` VARCHAR(100) NOT NULL,
    `shipping_address` TEXT NOT NULL,
    `shipping_phone` VARCHAR(30) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_orders_user` 
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_orders_user` (`user_id`),
    INDEX `idx_orders_status` (`status`),
    INDEX `idx_orders_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 6. Order Items Table: line items snapshotting product price at purchase
-- ----------------------------------------------------------------------------
CREATE TABLE `order_items` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `price_at_purchase` DECIMAL(10, 2) NOT NULL,
    `quantity` INT UNSIGNED NOT NULL,
    CONSTRAINT `fk_order_items_order` 
        FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_order_items_product` 
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_order_items_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 7. Order Status History Table: timeline audit trail of status transitions
-- ----------------------------------------------------------------------------
CREATE TABLE `order_status_history` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT UNSIGNED NOT NULL,
    `status` VARCHAR(50) NOT NULL,
    `note` TEXT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_status_history_order` 
        FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_history_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 8. Password Resets Table: cryptographic tokens for secure password recovery
-- ----------------------------------------------------------------------------
CREATE TABLE `password_resets` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `token_hash` VARCHAR(64) NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_password_resets_user` 
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_resets_token` (`token_hash`),
    INDEX `idx_resets_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

