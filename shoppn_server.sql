-- SERVER VERSION of shoppn_empty.sql, for restricted accounts on the school
-- server (MySQL error #1142 "DROP/REFERENCES command denied"). Differences:
--   * no DROP TABLE lines         (no DROP permission)
--   * no FOREIGN KEY constraints  (no REFERENCES permission) - the indexes
--     are kept; the PHP code must make sure ids are valid instead
--   * CREATE TABLE IF NOT EXISTS  (safe to re-run after a partial import)
-- ============================================================
-- shoppn e-commerce database — CLEAN SLATE (schema only, no data)
-- Import: phpMyAdmin → Import this file, OR:
--   mysql -u root < shoppn_clean.sql
--
-- Nothing is pre-created. You are responsible for setting up:
--   1. Your own admin account
--   2. Your own customer account(s)
--   3. Your own brands / categories / products
--
-- ── How to create the first admin account ──────────────────
--   a) Register normally on the site: /views/register.php
--      (every new signup is created as a regular customer, user_role = 2)
--   b) Promote that account to admin by running, in phpMyAdmin/mysql:
--        UPDATE customer SET user_role = 1 WHERE customer_email = 'you@example.com';
--   c) Log out and back in — the "Admin" link now appears in the header.
--
-- ── How to add products ─────────────────────────────────────
--   Brands and categories are empty too, and products require both
--   (foreign keys). As admin, add at least one brand and one category
--   first (Admin → Brands / Admin → Categories) before adding products.
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;



-- ── brands ──────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `brands` (
  `brand_id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(100) NOT NULL,
  PRIMARY KEY (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ── categories ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `categories` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(100) NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ── customer ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `customer` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(50) NOT NULL,
  `customer_pass` varchar(150) NOT NULL,
  `customer_country` varchar(30) NOT NULL,
  `customer_city` varchar(30) NOT NULL,
  `customer_contact` varchar(15) NOT NULL,
  `customer_image` varchar(100) DEFAULT NULL,
  `user_role` int(11) NOT NULL DEFAULT 2,
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `customer_email` (`customer_email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ── products ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_cat` int(11) NOT NULL,
  `product_brand` int(11) NOT NULL,
  `product_title` varchar(200) NOT NULL,
  `product_price` double NOT NULL,
  `product_desc` varchar(500) DEFAULT NULL,
  `product_image` varchar(100) DEFAULT NULL,
  `product_keywords` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`product_id`),
  KEY `product_cat` (`product_cat`),
  KEY `product_brand` (`product_brand`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ── cart ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `cart` (
  `p_id` int(11) NOT NULL,
  `ip_add` varchar(50) NOT NULL,
  `c_id` int(11) DEFAULT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  KEY `p_id` (`p_id`),
  KEY `c_id` (`c_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ── orders ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `invoice_no` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `order_status` varchar(100) NOT NULL DEFAULT 'paid',
  PRIMARY KEY (`order_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ── orderdetails ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `orderdetails` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ── payment ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `payment` (
  `pay_id` int(11) NOT NULL AUTO_INCREMENT,
  `amt` double NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `currency` text NOT NULL,
  `payment_date` date NOT NULL,
  PRIMARY KEY (`pay_id`),
  KEY `customer_id` (`customer_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

COMMIT;
