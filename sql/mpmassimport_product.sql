-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Nov 29, 2024 alle 10:05
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
-- Struttura della tabella `ps_mpmassimport_product`
--
DROP TABLE IF EXISTS `ps_mpmassimport_product`;

CREATE TABLE IF NOT EXISTS `ps_mpmassimport_product` (
    `id_mpmassimport_product` int(11) NOT NULL AUTO_INCREMENT,
    `id_supplier` int(11) NOT NULL,
    `reference` varchar(128) NOT NULL,
    `name` varchar(255) NOT NULL,
    `category` varchar(255) DEFAULT NULL,
    `id_category` int(11) DEFAULT NULL,
    `images` varchar(255) DEFAULT NULL,
    `price_original` decimal(20, 6) NOT NULL,
    `surcharge` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`surcharge`)),
    `price` decimal(20, 6) DEFAULT NULL,
    `quantity` int(11) NOT NULL,
    `content_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`content_json`)),
    `date_add` datetime NOT NULL,
    `date_upd` datetime DEFAULT NULL,
    PRIMARY KEY (`id_mpmassimport_product`),
    KEY `reference` (`reference`),
    KEY `id_supplier` (`id_supplier`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

SET
    FOREIGN_KEY_CHECKS = 1;

COMMIT;