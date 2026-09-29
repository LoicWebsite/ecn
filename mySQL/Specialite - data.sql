-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le : lun. 28 sep. 2026 à 14:34
-- Version du serveur : 5.7.44
-- Version de PHP : 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ECN`
--

--
-- Déchargement des données de la table `Specialite`
--

INSERT INTO `Specialite` (`CodeSpecialite`, `Specialite`, `Benefice`, `Type`, `Nature`, `Lieu`, `DureeInternat`) VALUES
('ACP', 'Anatomie et cytologie pathologiques', 190632, 'medecine', 'transversale', 'ville', 5),
('ALL', 'Allergologie', 84947, 'medecine', 'transversale', 'ville', 4),
('ARE', 'Anesthésie-réanimation', 209906, 'medecine', 'transversale', 'hopital', 5),
('BM', 'Biologie médicale', 106835, 'medecine', 'transversale', 'ville', 4),
('CMF', 'Chirurgie maxillo-faciale', 189736, 'chirurgie', 'chirurgicale', 'hopital', 6),
('COR', 'Chirurgie orale', 189736, 'chirurgie', 'chirurgicale', 'hopital', 4),
('COT', 'Chirurgie orthopédique et traumatologique', 189736, 'chirurgie', 'chirurgicale', 'hopital', 6),
('CPD', 'Chirurgie pédiatrique', 189736, 'chirurgie', 'chirurgicale', 'hopital', 6),
('CPR', 'Chirurgie plastique, reconstructrice et esthétique', 189736, 'chirurgie', 'chirurgicale', 'hopital', 6),
('CTC', 'Chirurgie thoracique et cardiovasculaire', 189736, 'chirurgie', 'chirurgicale', 'hopital', 6),
('CVA', 'Chirurgie vasculaire', 189736, 'chirurgie', 'chirurgicale', 'hopital', 6),
('CVD', 'Chirurgie viscérale et digestive', 189736, 'chirurgie', 'chirurgicale', 'hopital', 6),
('DVE', 'Dermatologie et vénéréologie', 103751, 'medecine', 'organe', 'ville', 4),
('EDN', 'Endocrinologie-diabétologie-nutrition', 79236, 'medecine', 'organe', 'ville', 4),
('GEN', 'Génétique médicale', 82718, 'medecine', 'transversale', 'hopital', 4),
('GER', 'Gériatrie', 82795, 'medecine', 'transversale', 'ville', 4),
('GYM', 'Gynécologie médicale', 78008, 'medecine', 'organe', 'ville', 4),
('GYO', 'Gynécologie obstétrique', 128601, 'mixte', 'chirurgicale', 'ville', 6),
('HEM', 'Hématologie', 100922, 'medecine', 'transversale', 'hopital', 5),
('HGE', 'Hépato-gastro-entérologie', 157985, 'medecine', 'organe', 'ville', 5),
('MCA', 'Médecine cardiovasculaire', 165950, 'medecine', 'organe', 'ville', 5),
('MGE', 'Médecine générale', 89238, 'medecine', 'transversale', 'ville', 4),
('MII', 'Médecine interne et immunologie clinique', 82484, 'medecine', 'transversale', 'hopital', 5),
('MIR', 'Médecine intensive-réanimation', 0, 'medecine', 'transversale', 'hopital', 5),
('MIT', 'Maladies infectieuses et tropicales', 84386, 'medecine', 'transversale', 'hopital', 5),
('MLE', 'Médecine légale et expertises médicales', 95728, 'medecine', 'transversale', 'hopital', 4),
('MPR', 'Médecine physique et de réadaptation', 92378, 'medecine', 'transversale', 'hopital', 4),
('MTR', 'Médecine et santé au travail', 0, 'medecine', 'transversale', 'autre', 4),
('MVA', 'Médecine vasculaire', 138493, 'medecine', 'organe', 'ville', 4),
('MUR', 'Médecine d’urgence', 62381, 'medecine', 'transversale', 'hopital', 4),
('NCU', 'Neurochirurgie', 126683, 'chirurgie', 'chirurgicale', 'hopital', 6),
('NEP', 'Néphrologie', 159268, 'medecine', 'organe', 'hopital', 4),
('NEU', 'Neurologie', 121181, 'medecine', 'organe', 'ville', 4),
('NUC', 'Médecine nucléaire', 231473, 'medecine', 'transversale', 'hopital', 4),
('ONC', 'Oncologie', 366246, 'medecine', 'transversale', 'ville', 5),
('OPH', 'Ophtalmologie', 200958, 'mixte', 'chirurgicale', 'ville', 6),
('ORL', 'Oto-rhino-laryngologie - chirurgie cervico-faciale', 139415, 'mixte', 'chirurgicale', 'ville', 6),
('PED', 'Pédiatrie', 86244, 'medecine', 'transversale', 'ville', 5),
('PNE', 'Pneumologie', 130928, 'medecine', 'organe', 'ville', 5),
('PSY', 'Psychiatrie', 92543, 'medecine', 'transversale', 'ville', 4),
('RAI', 'Radiologie et imagerie médicale', 192625, 'medecine', 'transversale', 'ville', 5),
('RHU', 'Rhumatologie', 95192, 'medecine', 'organe', 'ville', 4),
('SPU', 'Santé publique', 74018, 'medecine', 'transversale', 'autre', 4),
('URO', 'Urologie', 116305, 'mixte', 'chirurgicale', 'ville', 6);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
