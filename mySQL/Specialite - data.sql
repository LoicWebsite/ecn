-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le : dim. 01 nov. 2026 à 14:32
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
('ACP', 'Anatomie et cytologie pathologiques', 203895, 'medecine', 'transversale', 'ville', 5),
('ALL', 'Allergologie', 89130, 'medecine', 'transversale', 'ville', 4),
('ARE', 'Anesthésie-réanimation', 215912, 'medecine', 'transversale', 'hopital', 5),
('BM', 'Biologie médicale', 99599, 'medecine', 'transversale', 'ville', 4),
('CMF', 'Chirurgie maxillo-faciale', 195329, 'chirurgie', 'chirurgicale', 'hopital', 6),
('COR', 'Chirurgie orale', 195329, 'chirurgie', 'chirurgicale', 'hopital', 4),
('COT', 'Chirurgie orthopédique et traumatologique', 195329, 'chirurgie', 'chirurgicale', 'hopital', 6),
('CPD', 'Chirurgie pédiatrique', 195329, 'chirurgie', 'chirurgicale', 'hopital', 6),
('CPR', 'Chirurgie plastique, reconstructrice et esthétique', 195329, 'chirurgie', 'chirurgicale', 'hopital', 6),
('CTC', 'Chirurgie thoracique et cardiovasculaire', 195329, 'chirurgie', 'chirurgicale', 'hopital', 6),
('CVA', 'Chirurgie vasculaire', 195329, 'chirurgie', 'chirurgicale', 'hopital', 6),
('CVD', 'Chirurgie viscérale et digestive', 195329, 'chirurgie', 'chirurgicale', 'hopital', 6),
('DVE', 'Dermatologie et vénéréologie', 107139, 'medecine', 'organe', 'ville', 4),
('EDN', 'Endocrinologie-diabétologie-nutrition', 81252, 'medecine', 'organe', 'ville', 4),
('GEN', 'Génétique médicale', 91124, 'medecine', 'transversale', 'hopital', 4),
('GER', 'Gériatrie', 83814, 'medecine', 'transversale', 'ville', 4),
('GYM', 'Gynécologie médicale', 82283, 'medecine', 'organe', 'ville', 4),
('GYO', 'Gynécologie obstétrique', 128973, 'mixte', 'chirurgicale', 'ville', 6),
('HEM', 'Hématologie', 99392, 'medecine', 'transversale', 'hopital', 5),
('HGE', 'Hépato-gastro-entérologie', 157190, 'medecine', 'organe', 'ville', 5),
('MCA', 'Médecine cardiovasculaire', 170766, 'medecine', 'organe', 'ville', 5),
('MGE', 'Médecine générale', 97099, 'medecine', 'transversale', 'ville', 4),
('MII', 'Médecine interne et immunologie clinique', 88319, 'medecine', 'transversale', 'hopital', 5),
('MIR', 'Médecine intensive-réanimation', NULL, 'medecine', 'transversale', 'hopital', 5),
('MIT', 'Maladies infectieuses et tropicales', 86301, 'medecine', 'transversale', 'hopital', 5),
('MLE', 'Médecine légale et expertises médicales', 99571, 'medecine', 'transversale', 'hopital', 4),
('MPR', 'Médecine physique et de réadaptation', 93607, 'medecine', 'transversale', 'hopital', 4),
('MTR', 'Médecine et santé au travail', NULL, 'medecine', 'transversale', 'autre', 4),
('MVA', 'Médecine vasculaire', 142895, 'medecine', 'organe', 'ville', 4),
('MUR', 'Médecine d’urgence', 73782, 'medecine', 'transversale', 'hopital', 4),
('NCU', 'Neurochirurgie', 195329, 'chirurgie', 'chirurgicale', 'hopital', 6),
('NEP', 'Néphrologie', 168205, 'medecine', 'organe', 'hopital', 4),
('NEU', 'Neurologie', 121658, 'medecine', 'organe', 'ville', 4),
('NUC', 'Médecine nucléaire', 241751, 'medecine', 'transversale', 'hopital', 4),
('ONC', 'Oncologie', 405887, 'medecine', 'transversale', 'ville', 5),
('OPH', 'Ophtalmologie', 210776, 'mixte', 'chirurgicale', 'ville', 6),
('ORL', 'Oto-rhino-laryngologie - chirurgie cervico-faciale', 142093, 'mixte', 'chirurgicale', 'ville', 6),
('PED', 'Pédiatrie', 90202, 'medecine', 'transversale', 'ville', 5),
('PNE', 'Pneumologie', 134703, 'medecine', 'organe', 'ville', 5),
('PSY', 'Psychiatrie', 94524, 'medecine', 'transversale', 'ville', 4),
('RAI', 'Radiologie et imagerie médicale', 194605, 'medecine', 'transversale', 'ville', 5),
('RHU', 'Rhumatologie', 96075, 'medecine', 'organe', 'ville', 4),
('SPU', 'Santé publique', 86257, 'medecine', 'transversale', 'autre', 4),
('URO', 'Urologie', 134564, 'mixte', 'chirurgicale', 'ville', 6);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
