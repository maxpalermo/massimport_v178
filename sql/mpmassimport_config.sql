-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Nov 29, 2024 alle 11:04
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
-- Struttura della tabella `ps_mpmassimport_config`
--
DROP TABLE IF EXISTS `ps_mpmassimport_config`;

CREATE TABLE IF NOT EXISTS `ps_mpmassimport_config` (
    `id_mpmassimport_config` int(11) NOT NULL AUTO_INCREMENT,
    `type` varchar(32) NOT NULL,
    `url` text DEFAULT NULL,
    `file_name` varchar(255) DEFAULT NULL,
    `file_extract_path` varchar(255) DEFAULT NULL,
    `name` varchar(255) NOT NULL,
    `divider` varchar(32) NOT NULL,
    `category_divider` varchar(32) DEFAULT NULL,
    `headers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`headers`)),
    `categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`categories`)),
    `surcharge` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`surcharge`)),
    `id_supplier` int(11) DEFAULT NULL,
    `id_employee` int(11) NOT NULL,
    `date_add` datetime NOT NULL,
    `date_upd` datetime DEFAULT NULL,
    PRIMARY KEY (`id_mpmassimport_config`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

SET
    FOREIGN_KEY_CHECKS = 1;

COMMIT;