-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : jeu. 07 mars 2024 à 17:33
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

--
-- Déchargement des données de la table `CESP`
--

INSERT INTO `CESP` (`CodeSpecialite`, `CHU`, `CESP`) VALUES
('ARE', 'AMIENS', 0),
('COT', 'AMIENS', 0),
('DVE', 'AMIENS', 0),
('EDN', 'AMIENS', 0),
('GYM', 'AMIENS', 0),
('GYO', 'AMIENS', 0),
('HGE', 'AMIENS', 0),
('MCA', 'AMIENS', 0),
('MGE', 'AMIENS', 6),
('MUR', 'AMIENS', 0),
('NEU', 'AMIENS', 0),
('ONC', 'AMIENS', 0),
('OPH', 'AMIENS', 0),
('ORL', 'AMIENS', 0),
('PSY', 'AMIENS', 0),
('RAI', 'AMIENS', 0),
('URO', 'AMIENS', 0),
('ARE', 'ANGERS', 0),
('COT', 'ANGERS', 0),
('DVE', 'ANGERS', 0),
('EDN', 'ANGERS', 0),
('GYM', 'ANGERS', 0),
('GYO', 'ANGERS', 0),
('HGE', 'ANGERS', 0),
('MCA', 'ANGERS', 0),
('MGE', 'ANGERS', 6),
('MUR', 'ANGERS', 0),
('NEU', 'ANGERS', 0),
('ONC', 'ANGERS', 0),
('OPH', 'ANGERS', 0),
('ORL', 'ANGERS', 0),
('PSY', 'ANGERS', 0),
('RAI', 'ANGERS', 0),
('URO', 'ANGERS', 0),
('ARE', 'AP-HM MARSEILLE', 0),
('COT', 'AP-HM MARSEILLE', 1),
('DVE', 'AP-HM MARSEILLE', 1),
('EDN', 'AP-HM MARSEILLE', 0),
('GYM', 'AP-HM MARSEILLE', 0),
('GYO', 'AP-HM MARSEILLE', 0),
('HGE', 'AP-HM MARSEILLE', 0),
('MCA', 'AP-HM MARSEILLE', 0),
('MGE', 'AP-HM MARSEILLE', 12),
('MUR', 'AP-HM MARSEILLE', 1),
('NEU', 'AP-HM MARSEILLE', 0),
('ONC', 'AP-HM MARSEILLE', 0),
('OPH', 'AP-HM MARSEILLE', 0),
('ORL', 'AP-HM MARSEILLE', 0),
('PSY', 'AP-HM MARSEILLE', 0),
('RAI', 'AP-HM MARSEILLE', 0),
('URO', 'AP-HM MARSEILLE', 0),
('ARE', 'AP-HP PARIS', 0),
('COT', 'AP-HP PARIS', 0),
('DVE', 'AP-HP PARIS', 0),
('EDN', 'AP-HP PARIS', 0),
('GYM', 'AP-HP PARIS', 0),
('GYO', 'AP-HP PARIS', 0),
('HGE', 'AP-HP PARIS', 0),
('MCA', 'AP-HP PARIS', 0),
('MGE', 'AP-HP PARIS', 58),
('MUR', 'AP-HP PARIS', 0),
('NEU', 'AP-HP PARIS', 0),
('ONC', 'AP-HP PARIS', 0),
('OPH', 'AP-HP PARIS', 0),
('ORL', 'AP-HP PARIS', 0),
('PSY', 'AP-HP PARIS', 0),
('RAI', 'AP-HP PARIS', 0),
('URO', 'AP-HP PARIS', 0),
('ARE', 'BESANCON', 0),
('COT', 'BESANCON', 0),
('DVE', 'BESANCON', 0),
('EDN', 'BESANCON', 0),
('GYM', 'BESANCON', 0),
('GYO', 'BESANCON', 0),
('HGE', 'BESANCON', 0),
('MCA', 'BESANCON', 0),
('MGE', 'BESANCON', 3),
('MUR', 'BESANCON', 0),
('NEU', 'BESANCON', 0),
('ONC', 'BESANCON', 0),
('OPH', 'BESANCON', 0),
('ORL', 'BESANCON', 0),
('PSY', 'BESANCON', 0),
('RAI', 'BESANCON', 0),
('URO', 'BESANCON', 0),
('ARE', 'BORDEAUX', 0),
('COT', 'BORDEAUX', 0),
('DVE', 'BORDEAUX', 0),
('EDN', 'BORDEAUX', 0),
('GYM', 'BORDEAUX', 0),
('GYO', 'BORDEAUX', 0),
('HGE', 'BORDEAUX', 0),
('MCA', 'BORDEAUX', 0),
('MGE', 'BORDEAUX', 7),
('MUR', 'BORDEAUX', 0),
('NEU', 'BORDEAUX', 0),
('ONC', 'BORDEAUX', 0),
('OPH', 'BORDEAUX', 0),
('ORL', 'BORDEAUX', 0),
('PSY', 'BORDEAUX', 0),
('RAI', 'BORDEAUX', 0),
('URO', 'BORDEAUX', 0),
('ARE', 'BREST', 0),
('COT', 'BREST', 0),
('DVE', 'BREST', 0),
('EDN', 'BREST', 0),
('GYM', 'BREST', 0),
('GYO', 'BREST', 0),
('HGE', 'BREST', 0),
('MCA', 'BREST', 1),
('MGE', 'BREST', 4),
('MUR', 'BREST', 0),
('NEU', 'BREST', 0),
('ONC', 'BREST', 0),
('OPH', 'BREST', 0),
('ORL', 'BREST', 0),
('PSY', 'BREST', 0),
('RAI', 'BREST', 0),
('URO', 'BREST', 0),
('ARE', 'CAEN', 0),
('COT', 'CAEN', 0),
('DVE', 'CAEN', 0),
('EDN', 'CAEN', 0),
('GYM', 'CAEN', 0),
('GYO', 'CAEN', 0),
('HGE', 'CAEN', 0),
('MCA', 'CAEN', 0),
('MGE', 'CAEN', 4),
('MUR', 'CAEN', 1),
('NEU', 'CAEN', 0),
('ONC', 'CAEN', 0),
('OPH', 'CAEN', 0),
('ORL', 'CAEN', 0),
('PSY', 'CAEN', 0),
('RAI', 'CAEN', 0),
('URO', 'CAEN', 0),
('ARE', 'CLERMONT-FERRAND', 0),
('COT', 'CLERMONT-FERRAND', 0),
('DVE', 'CLERMONT-FERRAND', 0),
('EDN', 'CLERMONT-FERRAND', 0),
('GYM', 'CLERMONT-FERRAND', 0),
('GYO', 'CLERMONT-FERRAND', 0),
('HGE', 'CLERMONT-FERRAND', 0),
('MCA', 'CLERMONT-FERRAND', 0),
('MGE', 'CLERMONT-FERRAND', 4),
('MUR', 'CLERMONT-FERRAND', 0),
('NEU', 'CLERMONT-FERRAND', 0),
('ONC', 'CLERMONT-FERRAND', 0),
('OPH', 'CLERMONT-FERRAND', 0),
('ORL', 'CLERMONT-FERRAND', 0),
('PSY', 'CLERMONT-FERRAND', 0),
('RAI', 'CLERMONT-FERRAND', 0),
('URO', 'CLERMONT-FERRAND', 0),
('ARE', 'DIJON', 0),
('COT', 'DIJON', 0),
('DVE', 'DIJON', 0),
('EDN', 'DIJON', 0),
('GYM', 'DIJON', 0),
('GYO', 'DIJON', 0),
('HGE', 'DIJON', 0),
('MCA', 'DIJON', 0),
('MGE', 'DIJON', 4),
('MUR', 'DIJON', 0),
('NEU', 'DIJON', 0),
('ONC', 'DIJON', 0),
('OPH', 'DIJON', 0),
('ORL', 'DIJON', 0),
('PSY', 'DIJON', 0),
('RAI', 'DIJON', 0),
('URO', 'DIJON', 0),
('ARE', 'GRENOBLE', 0),
('COT', 'GRENOBLE', 0),
('DVE', 'GRENOBLE', 0),
('EDN', 'GRENOBLE', 0),
('GYM', 'GRENOBLE', 0),
('GYO', 'GRENOBLE', 0),
('HGE', 'GRENOBLE', 0),
('MCA', 'GRENOBLE', 0),
('MGE', 'GRENOBLE', 5),
('MUR', 'GRENOBLE', 0),
('NEU', 'GRENOBLE', 0),
('ONC', 'GRENOBLE', 0),
('OPH', 'GRENOBLE', 0),
('ORL', 'GRENOBLE', 0),
('PSY', 'GRENOBLE', 0),
('RAI', 'GRENOBLE', 0),
('URO', 'GRENOBLE', 0),
('ARE', 'HCL LYON', 0),
('COT', 'HCL LYON', 0),
('DVE', 'HCL LYON', 0),
('EDN', 'HCL LYON', 0),
('GYM', 'HCL LYON', 0),
('GYO', 'HCL LYON', 0),
('HGE', 'HCL LYON', 0),
('MCA', 'HCL LYON', 0),
('MGE', 'HCL LYON', 2),
('MUR', 'HCL LYON', 0),
('NEU', 'HCL LYON', 0),
('ONC', 'HCL LYON', 0),
('OPH', 'HCL LYON', 0),
('ORL', 'HCL LYON', 0),
('PSY', 'HCL LYON', 0),
('RAI', 'HCL LYON', 0),
('URO', 'HCL LYON', 0),
('ARE', 'LA REUNION', 0),
('COT', 'LA REUNION', 0),
('DVE', 'LA REUNION', 0),
('EDN', 'LA REUNION', 0),
('GYM', 'LA REUNION', 0),
('GYO', 'LA REUNION', 0),
('HGE', 'LA REUNION', 0),
('MCA', 'LA REUNION', 0),
('MGE', 'LA REUNION', 2),
('MUR', 'LA REUNION', 0),
('NEU', 'LA REUNION', 0),
('ONC', 'LA REUNION', 0),
('OPH', 'LA REUNION', 0),
('ORL', 'LA REUNION', 0),
('PSY', 'LA REUNION', 0),
('RAI', 'LA REUNION', 0),
('URO', 'LA REUNION', 0),
('ARE', 'LILLE', 0),
('COT', 'LILLE', 0),
('DVE', 'LILLE', 0),
('EDN', 'LILLE', 0),
('GYM', 'LILLE', 0),
('GYO', 'LILLE', 0),
('HGE', 'LILLE', 0),
('MCA', 'LILLE', 0),
('MGE', 'LILLE', 7),
('MUR', 'LILLE', 0),
('NEU', 'LILLE', 0),
('ONC', 'LILLE', 0),
('OPH', 'LILLE', 0),
('ORL', 'LILLE', 0),
('PSY', 'LILLE', 0),
('RAI', 'LILLE', 0),
('URO', 'LILLE', 0),
('ARE', 'LIMOGES', 0),
('COT', 'LIMOGES', 0),
('DVE', 'LIMOGES', 0),
('EDN', 'LIMOGES', 0),
('GYM', 'LIMOGES', 0),
('GYO', 'LIMOGES', 0),
('HGE', 'LIMOGES', 0),
('MCA', 'LIMOGES', 0),
('MGE', 'LIMOGES', 1),
('MUR', 'LIMOGES', 0),
('NEU', 'LIMOGES', 0),
('ONC', 'LIMOGES', 0),
('OPH', 'LIMOGES', 0),
('ORL', 'LIMOGES', 0),
('PSY', 'LIMOGES', 0),
('RAI', 'LIMOGES', 0),
('URO', 'LIMOGES', 0),
('ARE', 'MARTINIQUE/POINTE A PITRE', 1),
('COT', 'MARTINIQUE/POINTE A PITRE', 0),
('DVE', 'MARTINIQUE/POINTE A PITRE', 2),
('EDN', 'MARTINIQUE/POINTE A PITRE', 2),
('GYM', 'MARTINIQUE/POINTE A PITRE', 1),
('GYO', 'MARTINIQUE/POINTE A PITRE', 1),
('HGE', 'MARTINIQUE/POINTE A PITRE', 1),
('MCA', 'MARTINIQUE/POINTE A PITRE', 3),
('MGE', 'MARTINIQUE/POINTE A PITRE', 17),
('MUR', 'MARTINIQUE/POINTE A PITRE', 1),
('NEU', 'MARTINIQUE/POINTE A PITRE', 1),
('ONC', 'MARTINIQUE/POINTE A PITRE', 1),
('OPH', 'MARTINIQUE/POINTE A PITRE', 2),
('ORL', 'MARTINIQUE/POINTE A PITRE', 2),
('PSY', 'MARTINIQUE/POINTE A PITRE', 2),
('RAI', 'MARTINIQUE/POINTE A PITRE', 2),
('URO', 'MARTINIQUE/POINTE A PITRE', 1),
('ARE', 'MONTPELLIER', 0),
('COT', 'MONTPELLIER', 0),
('DVE', 'MONTPELLIER', 0),
('EDN', 'MONTPELLIER', 0),
('GYM', 'MONTPELLIER', 0),
('GYO', 'MONTPELLIER', 0),
('HGE', 'MONTPELLIER', 0),
('MCA', 'MONTPELLIER', 0),
('MGE', 'MONTPELLIER', 5),
('MUR', 'MONTPELLIER', 0),
('NEU', 'MONTPELLIER', 0),
('ONC', 'MONTPELLIER', 0),
('OPH', 'MONTPELLIER', 0),
('ORL', 'MONTPELLIER', 0),
('PSY', 'MONTPELLIER', 0),
('RAI', 'MONTPELLIER', 0),
('URO', 'MONTPELLIER', 0),
('ARE', 'NANCY', 0),
('COT', 'NANCY', 0),
('DVE', 'NANCY', 0),
('EDN', 'NANCY', 0),
('GYM', 'NANCY', 0),
('GYO', 'NANCY', 1),
('HGE', 'NANCY', 0),
('MCA', 'NANCY', 0),
('MGE', 'NANCY', 4),
('MUR', 'NANCY', 1),
('NEU', 'NANCY', 0),
('ONC', 'NANCY', 0),
('OPH', 'NANCY', 0),
('ORL', 'NANCY', 0),
('PSY', 'NANCY', 0),
('RAI', 'NANCY', 0),
('URO', 'NANCY', 0),
('ARE', 'NANTES', 0),
('COT', 'NANTES', 0),
('DVE', 'NANTES', 0),
('EDN', 'NANTES', 0),
('GYM', 'NANTES', 0),
('GYO', 'NANTES', 0),
('HGE', 'NANTES', 0),
('MCA', 'NANTES', 0),
('MGE', 'NANTES', 2),
('MUR', 'NANTES', 0),
('NEU', 'NANTES', 0),
('ONC', 'NANTES', 0),
('OPH', 'NANTES', 0),
('ORL', 'NANTES', 0),
('PSY', 'NANTES', 0),
('RAI', 'NANTES', 0),
('URO', 'NANTES', 0),
('ARE', 'NICE', 0),
('COT', 'NICE', 0),
('DVE', 'NICE', 0),
('EDN', 'NICE', 0),
('GYM', 'NICE', 0),
('GYO', 'NICE', 0),
('HGE', 'NICE', 0),
('MCA', 'NICE', 0),
('MGE', 'NICE', 7),
('MUR', 'NICE', 0),
('NEU', 'NICE', 0),
('ONC', 'NICE', 0),
('OPH', 'NICE', 0),
('ORL', 'NICE', 0),
('PSY', 'NICE', 0),
('RAI', 'NICE', 0),
('URO', 'NICE', 0),
('ARE', 'POITIERS', 0),
('COT', 'POITIERS', 0),
('DVE', 'POITIERS', 0),
('EDN', 'POITIERS', 0),
('GYM', 'POITIERS', 0),
('GYO', 'POITIERS', 0),
('HGE', 'POITIERS', 0),
('MCA', 'POITIERS', 0),
('MGE', 'POITIERS', 2),
('MUR', 'POITIERS', 0),
('NEU', 'POITIERS', 0),
('ONC', 'POITIERS', 0),
('OPH', 'POITIERS', 0),
('ORL', 'POITIERS', 0),
('PSY', 'POITIERS', 0),
('RAI', 'POITIERS', 0),
('URO', 'POITIERS', 0),
('ARE', 'REIMS', 0),
('COT', 'REIMS', 0),
('DVE', 'REIMS', 0),
('EDN', 'REIMS', 0),
('GYM', 'REIMS', 0),
('GYO', 'REIMS', 0),
('HGE', 'REIMS', 1),
('MCA', 'REIMS', 0),
('MGE', 'REIMS', 9),
('MUR', 'REIMS', 2),
('NEU', 'REIMS', 0),
('ONC', 'REIMS', 0),
('OPH', 'REIMS', 0),
('ORL', 'REIMS', 0),
('PSY', 'REIMS', 0),
('RAI', 'REIMS', 0),
('URO', 'REIMS', 0),
('ARE', 'RENNES', 0),
('COT', 'RENNES', 0),
('DVE', 'RENNES', 0),
('EDN', 'RENNES', 0),
('GYM', 'RENNES', 0),
('GYO', 'RENNES', 0),
('HGE', 'RENNES', 0),
('MCA', 'RENNES', 0),
('MGE', 'RENNES', 6),
('MUR', 'RENNES', 0),
('NEU', 'RENNES', 0),
('ONC', 'RENNES', 0),
('OPH', 'RENNES', 0),
('ORL', 'RENNES', 0),
('PSY', 'RENNES', 1),
('RAI', 'RENNES', 0),
('URO', 'RENNES', 0),
('ARE', 'ROUEN', 0),
('COT', 'ROUEN', 0),
('DVE', 'ROUEN', 0),
('EDN', 'ROUEN', 0),
('GYM', 'ROUEN', 0),
('GYO', 'ROUEN', 0),
('HGE', 'ROUEN', 0),
('MCA', 'ROUEN', 0),
('MGE', 'ROUEN', 5),
('MUR', 'ROUEN', 1),
('NEU', 'ROUEN', 0),
('ONC', 'ROUEN', 0),
('OPH', 'ROUEN', 0),
('ORL', 'ROUEN', 0),
('PSY', 'ROUEN', 1),
('RAI', 'ROUEN', 0),
('URO', 'ROUEN', 0),
('ARE', 'SAINT ETIENNE', 0),
('COT', 'SAINT ETIENNE', 0),
('DVE', 'SAINT ETIENNE', 0),
('EDN', 'SAINT ETIENNE', 0),
('GYM', 'SAINT ETIENNE', 0),
('GYO', 'SAINT ETIENNE', 0),
('HGE', 'SAINT ETIENNE', 0),
('MCA', 'SAINT ETIENNE', 0),
('MGE', 'SAINT ETIENNE', 3),
('MUR', 'SAINT ETIENNE', 0),
('NEU', 'SAINT ETIENNE', 0),
('ONC', 'SAINT ETIENNE', 0),
('OPH', 'SAINT ETIENNE', 0),
('ORL', 'SAINT ETIENNE', 0),
('PSY', 'SAINT ETIENNE', 0),
('RAI', 'SAINT ETIENNE', 0),
('URO', 'SAINT ETIENNE', 0),
('ARE', 'STRASBOURG', 0),
('COT', 'STRASBOURG', 0),
('DVE', 'STRASBOURG', 0),
('EDN', 'STRASBOURG', 0),
('GYM', 'STRASBOURG', 0),
('GYO', 'STRASBOURG', 0),
('HGE', 'STRASBOURG', 0),
('MCA', 'STRASBOURG', 0),
('MGE', 'STRASBOURG', 10),
('MUR', 'STRASBOURG', 0),
('NEU', 'STRASBOURG', 0),
('ONC', 'STRASBOURG', 0),
('OPH', 'STRASBOURG', 0),
('ORL', 'STRASBOURG', 0),
('PSY', 'STRASBOURG', 0),
('RAI', 'STRASBOURG', 0),
('URO', 'STRASBOURG', 0),
('ARE', 'TOULOUSE', 0),
('COT', 'TOULOUSE', 0),
('DVE', 'TOULOUSE', 0),
('EDN', 'TOULOUSE', 0),
('GYM', 'TOULOUSE', 0),
('GYO', 'TOULOUSE', 0),
('HGE', 'TOULOUSE', 0),
('MCA', 'TOULOUSE', 0),
('MGE', 'TOULOUSE', 10),
('MUR', 'TOULOUSE', 0),
('NEU', 'TOULOUSE', 0),
('ONC', 'TOULOUSE', 0),
('OPH', 'TOULOUSE', 0),
('ORL', 'TOULOUSE', 0),
('PSY', 'TOULOUSE', 0),
('RAI', 'TOULOUSE', 0),
('URO', 'TOULOUSE', 0),
('ARE', 'TOURS', 0),
('COT', 'TOURS', 0),
('DVE', 'TOURS', 0),
('EDN', 'TOURS', 0),
('GYM', 'TOURS', 0),
('GYO', 'TOURS', 0),
('HGE', 'TOURS', 0),
('MCA', 'TOURS', 1),
('MGE', 'TOURS', 8),
('MUR', 'TOURS', 0),
('NEU', 'TOURS', 0),
('ONC', 'TOURS', 0),
('OPH', 'TOURS', 0),
('ORL', 'TOURS', 0),
('PSY', 'TOURS', 1),
('RAI', 'TOURS', 0),
('URO', 'TOURS', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
