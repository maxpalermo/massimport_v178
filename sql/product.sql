-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Nov 28, 2024 alle 19:10
-- Versione del server: 10.6.18-MariaDB-0ubuntu0.22.04.1-log
-- Versione PHP: 8.1.31
SET
    FOREIGN_KEY_CHECKS = 0;

SET
    SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET
    time_zone = "+00:00";

--
-- Database: `ps_clienti_iacopo_comind`
--
-- --------------------------------------------------------
--
-- Struttura della tabella `ps_product`
--
DROP TABLE IF EXISTS `ps_product`;

CREATE TABLE `ps_product` (
    `id_product` int(10) UNSIGNED NOT NULL,
    `id_supplier` int(10) UNSIGNED NULL,
    `id_manufacturer` int(10) UNSIGNED NULL,
    `id_category_default` int(10) UNSIGNED NULL,
    `id_shop_default` int(10) UNSIGNED NOT NULL DEFAULT 1,
    `id_tax_rules_group` int(11) UNSIGNED NOT NULL,
    `on_sale` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
    `online_only` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
    `ean13` varchar(13) NULL,
    `upc` varchar(12) NULL,
    `mpn` varchar(40) NULL,
    `ecotax` decimal(17, 6) NOT NULL DEFAULT 0.000000,
    `quantity` int(10) NOT NULL DEFAULT 0,
    `minimal_quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
    `low_stock_threshold` int(10) NULL,
    `low_stock_alert` tinyint(1) NOT NULL DEFAULT 0,
    `price` decimal(20, 6) NOT NULL DEFAULT 0.000000,
    `wholesale_price` decimal(20, 6) NOT NULL DEFAULT 0.000000,
    `unity` varchar(255) NULL,
    `unit_price_ratio` decimal(20, 6) NOT NULL DEFAULT 0.000000,
    `additional_shipping_cost` decimal(20, 6) NOT NULL DEFAULT 0.000000,
    `reference` varchar(64) NULL,
    `supplier_reference` varchar(64) NULL,
    `location` varchar(255) NOT NULL DEFAULT '',
    `width` decimal(20, 6) NOT NULL DEFAULT 0.000000,
    `height` decimal(20, 6) NOT NULL DEFAULT 0.000000,
    `depth` decimal(20, 6) NOT NULL DEFAULT 0.000000,
    `weight` decimal(20, 6) NOT NULL DEFAULT 0.000000,
    `out_of_stock` int(10) UNSIGNED NOT NULL DEFAULT 2,
    `additional_delivery_times` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
    `quantity_discount` tinyint(1) DEFAULT 0,
    `customizable` tinyint(2) NOT NULL DEFAULT 0,
    `uploadable_files` tinyint(4) NOT NULL DEFAULT 0,
    `text_fields` tinyint(4) NOT NULL DEFAULT 0,
    `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
    `redirect_type` enum(
        '404',
        '301-product',
        '302-product',
        '301-category',
        '302-category'
    ) NOT NULL DEFAULT '404',
    `id_type_redirected` int(10) NOT NULL DEFAULT 0,
    `available_for_order` tinyint(1) NOT NULL DEFAULT 1,
    `available_date` date NULL,
    `show_condition` tinyint(1) NOT NULL DEFAULT 0,
    `condition` enum('new', 'used', 'refurbished') NOT NULL DEFAULT 'new',
    `show_price` tinyint(1) NOT NULL DEFAULT 1,
    `indexed` tinyint(1) NOT NULL DEFAULT 0,
    `visibility` enum('both', 'catalog', 'search', 'none') NOT NULL DEFAULT 'both',
    `cache_is_pack` tinyint(1) NOT NULL DEFAULT 0,
    `cache_has_attachments` tinyint(1) NOT NULL DEFAULT 0,
    `is_virtual` tinyint(1) NOT NULL DEFAULT 0,
    `cache_default_attribute` int(10) UNSIGNED NULL,
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    `advanced_stock_management` tinyint(1) NOT NULL DEFAULT 0,
    `pack_stock_type` int(11) UNSIGNED NOT NULL DEFAULT 3,
    `isbn` varchar(32) NULL,
    `state` int(10) UNSIGNED NOT NULL DEFAULT 1,
    `product_type` enum('standard', 'pack', 'virtual', 'combinations') NOT NULL DEFAULT 'standard'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Indici per le tabelle scaricate
--
--
-- Indici per le tabelle `ps_product`
--
ALTER TABLE
    `ps_product`
ADD
    PRIMARY KEY (`id_product`),
ADD
    KEY `product_supplier` (`id_supplier`),
ADD
    KEY `product_manufacturer` (`id_manufacturer`, `id_product`),
ADD
    KEY `id_category_default` (`id_category_default`),
ADD
    KEY `indexed` (`indexed`),
ADD
    KEY `date_add` (`date_add`),
ADD
    KEY `state` (`state`, `date_upd`),
ADD
    KEY `reference_idx` (`reference`),
ADD
    KEY `supplier_reference_idx` (`supplier_reference`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--
--
-- AUTO_INCREMENT per la tabella `ps_product`
--
ALTER TABLE
    `ps_product`
MODIFY
    `id_product` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

SET
    FOREIGN_KEY_CHECKS = 1;

COMMIT;