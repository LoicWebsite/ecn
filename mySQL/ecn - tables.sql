-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : jeu. 07 mars 2024 à 17:32
-- Version du serveur : 5.5.61-38.13-log
-- Version de PHP : 8.0.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ecn`
--

-- --------------------------------------------------------

--
-- Structure de la table `CESP`
--

CREATE TABLE `CESP` (
  `CodeSpecialite` varchar(3) COLLATE utf8_roman_ci DEFAULT NULL,
  `CHU` varchar(25) COLLATE utf8_roman_ci DEFAULT NULL,
  `CESP` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_roman_ci;

-- --------------------------------------------------------

--
-- Structure de la table `MiseAJour`
--

CREATE TABLE `MiseAJour` (
  `Id` int(1) NOT NULL,
  `DateMiseAJour` varchar(8) COLLATE utf8_roman_ci DEFAULT NULL,
  `HeureMiseAJour` varchar(6) COLLATE utf8_roman_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_roman_ci;

-- --------------------------------------------------------

--
-- Structure de la table `Poste`
--

CREATE TABLE `Poste` (
  `CodeSpecialite` varchar(3) COLLATE utf8_roman_ci DEFAULT NULL,
  `CHU` varchar(25) COLLATE utf8_roman_ci DEFAULT NULL,
  `Poste` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_roman_ci;

-- --------------------------------------------------------

--
-- Structure de la table `Rang`
--

CREATE TABLE `Rang` (
  `CodeSpecialite` varchar(3) COLLATE utf8_roman_ci DEFAULT NULL,
  `CHU` varchar(25) COLLATE utf8_roman_ci DEFAULT NULL,
  `Poste2023` int(4) DEFAULT NULL,
  `Poste2022` int(4) DEFAULT '0',
  `Poste2021` int(4) DEFAULT NULL,
  `Poste2020` int(3) DEFAULT NULL,
  `Dernier2023` int(4) DEFAULT NULL,
  `Dernier2022` int(4) DEFAULT NULL,
  `Dernier2021` int(4) DEFAULT NULL,
  `Dernier2020` int(4) DEFAULT '0',
  `Dernier2019` int(4) DEFAULT NULL,
  `Dernier2018` int(4) DEFAULT NULL,
  `Dernier2017` int(4) DEFAULT NULL,
  `URLCeline` varchar(60) COLLATE utf8_roman_ci DEFAULT NULL,
  `CESP2023` int(3) NOT NULL DEFAULT '0',
  `CESP2022` int(3) DEFAULT '0',
  `CESP2021` int(3) DEFAULT NULL,
  `CESP2020` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_roman_ci;

-- --------------------------------------------------------

--
-- Structure de la table `Specialite`
--

CREATE TABLE `Specialite` (
  `CodeSpecialite` varchar(3) COLLATE utf8_roman_ci NOT NULL,
  `Specialite` varchar(50) COLLATE utf8_roman_ci DEFAULT NULL,
  `Poste2023` int(4) DEFAULT NULL,
  `Poste2022` int(4) DEFAULT '0',
  `Poste2021` int(4) DEFAULT NULL,
  `Poste2020` int(4) DEFAULT NULL,
  `CESP2023` int(3) DEFAULT NULL,
  `CESP2022` int(3) DEFAULT '0',
  `CESP2021` int(3) DEFAULT NULL,
  `CESP2020` int(3) DEFAULT NULL,
  `Dernier2023` int(4) DEFAULT NULL,
  `CHUDernier2023` varchar(27) COLLATE utf8_roman_ci DEFAULT NULL,
  `Dernier2022` int(4) DEFAULT NULL,
  `CHUDernier2022` varchar(27) COLLATE utf8_roman_ci DEFAULT NULL,
  `Dernier2021` int(4) DEFAULT NULL,
  `CHUDernier2021` varchar(27) COLLATE utf8_roman_ci DEFAULT NULL,
  `Dernier2020` int(4) NOT NULL DEFAULT '0',
  `CHUDernier2020` varchar(27) COLLATE utf8_roman_ci DEFAULT NULL,
  `Dernier2019` int(4) DEFAULT NULL,
  `CHUDernier2019` varchar(27) COLLATE utf8_roman_ci DEFAULT NULL,
  `Dernier2018` int(4) DEFAULT NULL,
  `CHUDernier2018` varchar(27) COLLATE utf8_roman_ci DEFAULT NULL,
  `Dernier2017` int(4) DEFAULT NULL,
  `CHUDernier2017` varchar(27) COLLATE utf8_roman_ci DEFAULT NULL,
  `Benefice` int(6) DEFAULT NULL,
  `Type` varchar(9) COLLATE utf8_roman_ci DEFAULT NULL,
  `Nature` varchar(12) COLLATE utf8_roman_ci DEFAULT NULL,
  `Lieu` varchar(7) COLLATE utf8_roman_ci DEFAULT NULL,
  `DureeInternat` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_roman_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `MiseAJour`
--
ALTER TABLE `MiseAJour`
  ADD PRIMARY KEY (`Id`);

--
-- Index pour la table `Specialite`
--
ALTER TABLE `Specialite`
  ADD PRIMARY KEY (`CodeSpecialite`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
