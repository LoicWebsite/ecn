-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : dim. 06 avr. 2025 à 15:10
-- Version du serveur : 5.5.61-38.13-log
-- Version de PHP : 8.3.19

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
-- Déchargement des données de la table `Specialite`
--

INSERT INTO `Specialite` (`CodeSpecialite`, `Specialite`, `Poste2024`, `Poste2023`, `Poste2022`, `Poste2021`, `Poste2020`, `CESP2024`, `CESP2023`, `CESP2022`, `CESP2021`, `CESP2020`, `Dernier2024`, `CHUDernier2024`, `Dernier2023`, `CHUDernier2023`, `Dernier2022`, `CHUDernier2022`, `Dernier2021`, `CHUDernier2021`, `Dernier2020`, `CHUDernier2020`, `Dernier2019`, `CHUDernier2019`, `Dernier2018`, `CHUDernier2018`, `Dernier2017`, `CHUDernier2017`, `Benefice`, `Type`, `Nature`, `Lieu`, `DureeInternat`) VALUES
('ACP', 'Anatomie et cytologie pathologiques', 60, 70, 59, 59, 59, 0, 0, 0, NULL, NULL, 5561, 'MARTINIQUE/POINTE A PITRE', 5082, 'BREST', 5708, 'MARTINIQUE/POINTE A PITRE', 7126, 'MARTINIQUE/POINTE A PITRE', 5182, 'BESANCON', 5549, ' MARTINIQUE/POINTE A PITRE ', 5611, ' BESANCON ', 4199, ' BESANCON ', 108640, 'medecine', 'transversale', 'ville', 5),
('ALL', 'Allergologie', 29, 35, 28, 28, 28, 0, 0, 0, NULL, NULL, 6332, 'SAINT ETIENNE', 7330, 'BREST', 8398, 'BREST', 8534, 'CLERMONT-FERRAND', 6545, 'BREST', 7529, ' LIMOGES ', 6682, ' BESANCON ', 6399, ' BREST ', 61144, 'medecine', 'transversale', 'ville', 4),
('ARE', 'Anesthésie-réanimation', 428, 509, 492, 486, 468, 3, 1, 3, 3, 5, 2551, 'LIMOGES', 3648, 'LIMOGES', 3465, 'LIMOGES', 3714, 'LIMOGES', 3424, 'LIMOGES', 3698, ' LIMOGES ', 4079, ' BESANCON ', 4083, ' LIMOGES ', 148638, 'medecine', 'transversale', 'hopital', 5),
('BM', 'Biologie médicale', 87, 107, 107, 107, 107, 0, 0, 0, NULL, NULL, 7813, 'AMIENS', 10500, 'MARTINIQUE/POINTE A PITRE', 9293, 'AP-HP Paris', 8931, 'AMIENS', 8780, 'DIJON', 8714, ' LILLE ', 8695, ' MONTPELLIER ', 8363, ' SAINT ETIENNE ', 96561, 'medecine', 'transversale', 'ville', 4),
('CMF', 'Chirurgie maxillo-faciale', 22, 27, 26, 26, 26, 0, 0, 0, NULL, NULL, 2393, 'NANCY', 2206, 'NANCY', 3298, 'AMIENS', 3128, 'STRASBOURG', 3289, 'MARTINIQUE/POINTE A PITRE', 2426, ' ROUEN ', 3250, ' LIMOGES ', 2870, ' ROUEN ', 126683, 'chirurgie', 'chirurgicale', 'hopital', 6),
('COR', 'Chirurgie orale', 12, 16, 14, 12, 12, 0, 0, 0, NULL, NULL, 3333, 'TOURS', 3772, 'NANCY', 3936, 'CLERMONT-FERRAND', 3913, 'DIJON', 3633, 'DIJON', 4556, ' CAEN ', 4127, ' BESANCON ', 3191, ' STRASBOURG ', 126683, 'chirurgie', 'chirurgicale', 'hopital', 4),
('COT', 'Chirurgie orthopédique et traumatologique', 107, 129, 126, 123, 121, 1, 1, 1, NULL, NULL, 3170, 'CLERMONT-FERRAND', 3573, 'MARTINIQUE/POINTE A PITRE', 3870, 'LA REUNION', 3838, 'BESANCON', 4008, 'LIMOGES', 3834, ' LIMOGES ', 3887, ' LA REUNION ', 3467, ' LIMOGES ', 142894, 'chirurgie', 'chirurgicale', 'hopital', 6),
('CPD', 'Chirurgie pédiatrique', 33, 33, 29, 26, 23, 0, 0, 0, NULL, NULL, 3912, 'AMIENS', 4775, 'LIMOGES', 4230, 'DIJON', 4299, 'CLERMONT-FERRAND', 4820, 'LIMOGES', 4080, ' MARTINIQUE/POINTE A PITRE ', 5169, ' LIMOGES ', 4103, ' LIMOGES ', 126683, 'chirurgie', 'chirurgicale', 'hopital', 6),
('CPR', 'Chirurgie plastique, reconstructrice et esthétique', 14, 28, 28, 28, 28, 0, 0, 0, NULL, NULL, 1166, 'REIMS', 1524, 'REIMS', 1357, 'POITIERS', 1649, 'BESANCON', 948, 'REIMS', 1807, ' REIMS ', 2177, ' REIMS ', 1553, ' NANCY ', 126683, 'chirurgie', 'chirurgicale', 'hopital', 6),
('CTC', 'Chirurgie thoracique et cardiovasculaire', 21, 25, 25, 25, 25, 0, 0, 0, NULL, NULL, 3901, 'ROUEN', 4952, 'LA REUNION', 5205, 'MARTINIQUE/POINTE A PITRE', 5411, 'LA REUNION', 4927, 'MARTINIQUE/POINTE A PITRE', 4810, ' LA REUNION ', 5624, ' MARTINIQUE/POINTE A PITRE ', 4510, ' LIMOGES ', 126683, 'chirurgie', 'chirurgicale', 'hopital', 6),
('CVA', 'Chirurgie vasculaire', 23, 28, 28, 28, 27, 0, 0, 0, NULL, NULL, 3902, 'LIMOGES', 4274, 'ROUEN', 4902, 'DIJON', 5182, 'BREST', 4610, 'NANCY', 4486, ' BREST ', 4505, ' NANCY ', 4439, ' LIMOGES ', 126683, 'chirurgie', 'chirurgicale', 'hopital', 6),
('CVD', 'Chirurgie viscérale et digestive', 75, 90, 87, 83, 79, 0, 0, 0, NULL, NULL, 3801, 'NANCY', 5078, 'MARTINIQUE/POINTE A PITRE', 4825, 'BESANCON', 5177, 'LA REUNION', 4783, 'LA REUNION', 4778, ' LA REUNION ', 5247, ' MARTINIQUE/POINTE A PITRE ', 4509, ' MARTINIQUE/POINTE A PITRE ', 126683, 'chirurgie', 'chirurgicale', 'hopital', 6),
('DVE', 'Dermatologie et vénéréologie', 91, 110, 103, 100, 93, 3, 3, 1, NULL, 1, 1212, 'NANCY', 1920, 'MARTINIQUE/POINTE A PITRE', 2403, 'MARTINIQUE/POINTE A PITRE', 2797, 'MARTINIQUE/POINTE A PITRE', 1810, 'LIMOGES', 2277, ' LIMOGES ', 2448, ' MARTINIQUE/POINTE A PITRE ', 2247, ' LIMOGES ', 76769, 'medecine', 'organe', 'ville', 4),
('EDN', 'Endocrinologie-diabétologie-nutrition', 84, 101, 94, 89, 84, 2, 2, 0, NULL, NULL, 5180, 'MARTINIQUE/POINTE A PITRE', 6034, 'SAINT ETIENNE', 6846, 'LA REUNION', 7403, 'TOURS', 6112, 'MARTINIQUE/POINTE A PITRE', 6522, ' SAINT ETIENNE ', 6667, ' BREST ', 5752, ' MARTINIQUE/POINTE A PITRE ', 59841, 'medecine', 'organe', 'ville', 4),
('GEN', 'Génétique médicale', 21, 25, 21, 21, 20, 0, 0, 0, NULL, NULL, 7495, 'LIMOGES', 9515, 'AMIENS', 9288, 'MARTINIQUE/POINTE A PITRE', 8459, 'LA REUNION', 7008, 'LIMOGES', 7837, ' AMIENS ', 8470, ' AMIENS ', 5801, ' BESANCON ', 96119, 'medecine', 'transversale', 'hopital', 4),
('GER', 'Gériatrie', 163, 196, 194, 193, 192, 0, 0, 0, 1, 2, 7812, 'BESANCON', 10500, 'MARTINIQUE/POINTE A PITRE', 9290, 'AP-HP Paris', 9500, 'CAEN', 8809, 'POITIERS', 8728, 'LIMOGES', 8684, ' REIMS ', 8371, ' CAEN ', 62586, 'medecine', 'transversale', 'ville', 4),
('GYM', 'Gynécologie médicale', 73, 90, 87, 84, 82, 1, 1, 0, 2, 2, 3546, 'MARTINIQUE/POINTE A PITRE', 4581, 'NANCY', 4754, 'LIMOGES', 4542, 'MARTINIQUE/POINTE A PITRE', 4443, 'REIMS', 4743, ' MARTINIQUE/POINTE A PITRE ', 5331, ' MARTINIQUE/POINTE A PITRE ', 4600, ' MARTINIQUE/POINTE A PITRE ', 58003, 'medecine', 'organe', 'ville', 4),
('GYO', 'Gynécologie obstétrique', 200, 235, 219, 211, 208, 3, 2, 3, 2, 1, 3499, 'BESANCON', 4565, 'AMIENS', 4288, 'POITIERS', 4717, 'LIMOGES', 4259, 'AMIENS', 4623, ' MARTINIQUE/POINTE A PITRE ', 4843, ' LA REUNION ', 4538, ' LA REUNION ', 99997, 'mixte', 'chirurgicale', 'ville', 6),
('HEM', 'Hématologie', 45, 54, 49, 45, 43, 0, 0, 0, NULL, NULL, 4491, 'SAINT ETIENNE', 5813, 'REIMS', 6173, 'MARTINIQUE/POINTE A PITRE', 7110, 'LA REUNION', 4876, 'MARTINIQUE/POINTE A PITRE', 6292, ' MARTINIQUE/POINTE A PITRE ', 5790, ' MARTINIQUE/POINTE A PITRE ', 5628, ' AMIENS ', 105199, 'medecine', 'transversale', 'hopital', 5),
('HGE', 'Hépato-gastro-entérologie', 122, 145, 138, 132, 127, 1, 2, 0, 1, 1, 3343, 'MARTINIQUE/POINTE A PITRE', 4234, 'MARTINIQUE/POINTE A PITRE', 4465, 'CLERMONT-FERRAND', 4475, 'BESANCON', 3643, 'LIMOGES', 3898, ' LIMOGES ', 4332, ' MARTINIQUE/POINTE A PITRE ', 3971, ' LIMOGES ', 126094, 'medecine', 'organe', 'ville', 5),
('MCA', 'Médecine cardiovasculaire', 169, 199, 193, 184, 178, 1, 5, 1, 1, 2, 2694, 'AMIENS', 3142, 'LIMOGES', 3363, 'MARTINIQUE/POINTE A PITRE', 3394, 'LIMOGES', 2567, 'AMIENS', 3445, ' LIMOGES ', 2678, ' MARTINIQUE/POINTE A PITRE ', 2887, ' LIMOGES ', 132863, 'medecine', 'organe', 'ville', 5),
('MGE', 'Médecine générale', 2963, 3645, 3388, 3280, 3177, 258, 213, 246, 238, 256, 7811, 'TOURS', 9716, 'MARTINIQUE/POINTE A PITRE', 9014, 'LIMOGES', 8862, 'LIMOGES', 8820, 'TOURS', 8676, ' MARTINIQUE/POINTE A PITRE ', 8706, ' REIMS ', 8372, ' BREST ', 73820, 'medecine', 'transversale', 'ville', 4),
('MII', 'Médecine interne et immunologie clinique', 114, 137, 134, 130, 123, 0, 0, 0, NULL, NULL, 5654, 'MARTINIQUE/POINTE A PITRE', 6870, 'BESANCON', 7409, 'AMIENS', 8778, 'BESANCON', 5507, 'LIMOGES', 6456, ' BESANCON ', 6801, ' BESANCON ', 5693, ' AMIENS ', 70416, 'medecine', 'transversale', 'hopital', 5),
('MIR', 'Médecine intensive-réanimation', 90, 105, 101, 95, 74, 0, 0, 0, NULL, NULL, 3584, 'SAINT ETIENNE', 4972, 'MARTINIQUE/POINTE A PITRE', 5340, 'LIMOGES', 5866, 'LIMOGES', 4707, 'LIMOGES', 4896, ' LIMOGES ', 5318, ' CLERMONT-FERRAND ', 4271, ' AMIENS ', 0, 'medecine', 'transversale', 'hopital', 5),
('MIT', 'Maladies infectieuses et tropicales', 50, 60, 56, 54, 52, 0, 0, 0, NULL, NULL, 5244, 'MARTINIQUE/POINTE A PITRE', 5064, 'MARTINIQUE/POINTE A PITRE', 5931, 'MARTINIQUE/POINTE A PITRE', 7394, 'LIMOGES', 4503, 'AMIENS', 5238, ' MARTINIQUE/POINTE A PITRE ', 3209, ' LIMOGES ', 3709, ' MARTINIQUE/POINTE A PITRE ', 0, 'medecine', 'transversale', 'hopital', 5),
('MLE', 'Médecine légale et expertises médicales', 23, 28, 26, 26, 26, 0, 0, 0, NULL, NULL, 7444, 'MARTINIQUE/POINTE A PITRE', 7854, 'MARTINIQUE/POINTE A PITRE', 9062, 'MARTINIQUE/POINTE A PITRE', 8876, 'AMIENS', 7160, 'MARTINIQUE/POINTE A PITRE', 7345, ' CAEN ', 7618, ' LIMOGES ', 7441, ' CAEN ', 70594, 'medecine', 'transversale', 'hopital', 4),
('MPR', 'Médecine physique et de réadaptation', 90, 108, 103, 101, 98, 0, 0, 0, NULL, NULL, 6932, 'LIMOGES', 7463, 'BESANCON', 8909, 'AMIENS', 7694, 'LIMOGES', 7570, 'AMIENS', 7300, ' AMIENS ', 7068, ' BESANCON ', 5926, ' AMIENS ', 69582, 'medecine', 'transversale', 'hopital', 4),
('MTR', 'Médecine et santé au travail', 93, 116, 116, 124, 124, 0, 0, 0, 1, 1, 7817, 'TOULOUSE', 10500, 'MARTINIQUE/POINTE A PITRE', 9285, 'NANTES', 9500, 'CAEN', 8794, 'AP-HP Paris', 8721, ' RENNES ', 8699, ' BREST ', 8370, ' NANCY ', 0, 'medecine', 'transversale', 'autre', 4),
('MVA', 'Médecine vasculaire', 40, 49, 48, 46, 45, 0, 0, 0, NULL, NULL, 3661, 'MARTINIQUE/POINTE A PITRE', 4555, 'MARTINIQUE/POINTE A PITRE', 4809, 'MARTINIQUE/POINTE A PITRE', 4746, 'BREST', 4373, 'NANCY', 4691, ' MARTINIQUE/POINTE A PITRE ', 4633, ' POITIERS ', 4635, ' LIMOGES ', 99934, 'medecine', 'organe', 'ville', 4),
('MUR', 'Médecine d’urgence', 414, 487, 483, 474, 471, 2, 7, 4, 5, 5, 6288, 'AMIENS', 8007, 'BESANCON', 9247, 'CAEN', 8452, 'AP-HP Paris', 8399, 'REIMS', 8708, ' DIJON ', 8693, ' BESANCON ', 8285, ' LIMOGES ', 95863, 'medecine', 'transversale', 'hopital', 4),
('NCU', 'Neurochirurgie', 26, 28, 27, 25, 25, 0, 0, 0, NULL, NULL, 3687, 'MARTINIQUE/POINTE A PITRE', 4760, 'CLERMONT-FERRAND', 5083, 'CLERMONT-FERRAND', 4738, 'POITIERS', 4872, 'BESANCON', 5112, ' MARTINIQUE/POINTE A PITRE ', 5595, ' MARTINIQUE/POINTE A PITRE ', 3986, ' DIJON ', 126683, 'chirurgie', 'chirurgicale', 'hopital', 6),
('NEP', 'Néphrologie', 75, 91, 86, 81, 79, 0, 0, 0, NULL, NULL, 4098, 'LIMOGES', 4629, 'MARTINIQUE/POINTE A PITRE', 4347, 'MARTINIQUE/POINTE A PITRE', 4377, 'LIMOGES', 4174, 'MARTINIQUE/POINTE A PITRE', 4655, ' MARTINIQUE/POINTE A PITRE ', 3791, ' MARTINIQUE/POINTE A PITRE ', 4395, ' LIMOGES ', 145354, 'medecine', 'organe', 'hopital', 4),
('NEU', 'Neurologie', 119, 142, 135, 128, 125, 0, 1, 0, 2, 0, 4083, 'MARTINIQUE/POINTE A PITRE', 4715, 'MARTINIQUE/POINTE A PITRE', 5126, 'LIMOGES', 5188, 'LA REUNION', 4037, 'AMIENS', 4688, ' MARTINIQUE/POINTE A PITRE ', 4280, ' REIMS ', 3643, ' BESANCON ', 100114, 'medecine', 'organe', 'ville', 4),
('NUC', 'Médecine nucléaire', 31, 35, 33, 33, 32, 0, 0, 0, NULL, NULL, 2966, 'AMIENS', 3418, 'NANCY', 3450, 'BESANCON', 3736, 'LIMOGES', 3564, 'BESANCON', 3555, ' CAEN ', 3797, ' LIMOGES ', 3122, ' MARTINIQUE/POINTE A PITRE ', 125829, 'medecine', 'transversale', 'hopital', 4),
('ONC', 'Oncologie', 110, 131, 126, 121, 118, 0, 1, 0, NULL, NULL, 3424, 'AMIENS', 4359, 'LIMOGES', 4034, 'LIMOGES', 4788, 'LA REUNION', 3743, 'BREST', 4087, ' LIMOGES ', 3935, ' LIMOGES ', 4146, ' LIMOGES ', 262576, 'medecine', 'transversale', 'ville', 5),
('OPH', 'Ophtalmologie', 125, 153, 154, 152, 148, 1, 2, 1, 3, 4, 1523, 'SAINT ETIENNE', 2169, 'SAINT ETIENNE', 2521, 'MARTINIQUE/POINTE A PITRE', 1730, 'LIMOGES', 2166, 'LIMOGES', 2496, ' MARTINIQUE/POINTE A PITRE ', 1830, ' BESANCON ', 2158, ' MARTINIQUE/POINTE A PITRE ', 130707, 'mixte', 'chirurgicale', 'ville', 6),
('ORL', 'Oto-rhino-laryngologie - chirurgie cervico-faciale', 74, 88, 86, 83, 80, 0, 2, 1, NULL, NULL, 2486, 'MARTINIQUE/POINTE A PITRE', 3358, 'LIMOGES', 3203, 'DIJON', 3518, 'MARTINIQUE/POINTE A PITRE', 3287, 'DIJON', 3283, ' LIMOGES ', 3254, ' BESANCON ', 3147, ' BREST ', 97736, 'mixte', 'chirurgicale', 'ville', 6),
('PED', 'Pédiatrie', 350, 377, 342, 336, 328, 1, 0, 3, NULL, 3, 5177, 'MARTINIQUE/POINTE A PITRE', 6271, 'BESANCON', 6780, 'LIMOGES', 6591, 'MARTINIQUE/POINTE A PITRE', 5824, 'LIMOGES', 5790, ' LIMOGES ', 6141, ' LIMOGES ', 5129, ' BESANCON ', 67483, 'medecine', 'transversale', 'ville', 5),
('PNE', 'Pneumologie', 114, 138, 130, 123, 119, 1, 0, 0, 1, 1, 3822, 'SAINT ETIENNE', 4877, 'MARTINIQUE/POINTE A PITRE', 5165, 'LIMOGES', 5725, 'BESANCON', 4578, 'BREST', 4533, ' LA REUNION ', 4557, ' LIMOGES ', 4624, ' MARTINIQUE/POINTE A PITRE ', 98820, 'medecine', 'organe', 'ville', 5),
('PSY', 'Psychiatrie', 485, 547, 534, 532, 527, 4, 5, 5, 4, 4, 7803, 'NANCY', 10500, 'MARTINIQUE/POINTE A PITRE', 9294, 'NANCY', 9500, 'CAEN', 8816, 'DIJON', 8728, ' MARTINIQUE/POINTE A PITRE ', 8685, ' BESANCON ', 8373, ' AMIENS ', 70711, 'medecine', 'transversale', 'ville', 4),
('RAI', 'Radiologie et imagerie médicale', 223, 271, 260, 256, 252, 2, 2, 1, 2, 1, 3250, 'LIMOGES', 3407, 'BESANCON', 3653, 'BREST', 3392, 'LIMOGES', 3631, 'LIMOGES', 3808, ' MARTINIQUE/POINTE A PITRE ', 3076, ' CAEN ', 2536, ' LIMOGES ', 103804, 'medecine', 'transversale', 'ville', 5),
('RHU', 'Rhumatologie', 78, 94, 88, 86, 84, 0, 0, 0, NULL, 1, 4109, 'MARTINIQUE/POINTE A PITRE', 5268, 'MARTINIQUE/POINTE A PITRE', 5254, 'LIMOGES', 5567, 'MARTINIQUE/POINTE A PITRE', 4407, 'SAINT ETIENNE', 4779, ' LIMOGES ', 4159, ' AMIENS ', 4290, ' LIMOGES ', 72380, 'medecine', 'organe', 'ville', 4),
('SPU', 'Santé publique', 69, 87, 87, 87, 87, 1, 0, 0, NULL, NULL, 7787, 'AP-HM Marseille', 9705, 'LILLE', 9298, 'AP-HM Marseille', 9016, 'STRASBOURG', 8818, 'ROUEN', 8723, ' ROUEN ', 8674, ' STRASBOURG ', 8211, ' CLERMONT-FERRAND ', 0, 'medecine', 'transversale', 'autre', 4),
('URO', 'Urologie', 54, 63, 64, 62, 61, 0, 2, 0, NULL, NULL, 3128, 'AMIENS', 3821, 'AMIENS', 4289, 'LIMOGES', 4805, 'LIMOGES', 4201, 'AMIENS', 4497, ' LA REUNION ', 4088, ' LA REUNION ', 3888, ' LIMOGES ', 116305, 'mixte', 'chirurgicale', 'ville', 6);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
