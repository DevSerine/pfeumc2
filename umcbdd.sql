-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 17, 2025 at 10:23 AM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `umcbdd`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int NOT NULL,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `date_creation`) VALUES
(1, '2025-01-17 11:09:13');

-- --------------------------------------------------------

--
-- Table structure for table `apprenant`
--

DROP TABLE IF EXISTS `apprenant`;
CREATE TABLE IF NOT EXISTS `apprenant` (
  `id` int NOT NULL,
  `formation_id` int DEFAULT NULL,
  `type_apprenant` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `entreprise`
--

DROP TABLE IF EXISTS `entreprise`;
CREATE TABLE IF NOT EXISTS `entreprise` (
  `id` int NOT NULL,
  `formation_id` int DEFAULT NULL,
  `nb_employes` int DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `formateur`
--

DROP TABLE IF EXISTS `formateur`;
CREATE TABLE IF NOT EXISTS `formateur` (
  `id` int NOT NULL,
  `cv` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `formation`
--

DROP TABLE IF EXISTS `formation`;
CREATE TABLE IF NOT EXISTS `formation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `theme_id` int NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `duree` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `date_creation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('disponible','indisponible') NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `theme_id` (`theme_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `formation`
--

INSERT INTO `formation` (`id`, `nom`, `theme_id`, `description`, `image`, `duree`, `code`, `date_creation`, `statut`, `date_debut`, `date_fin`) VALUES
(1, 'Administration des systèmes Linux Redhat', 1, 'Cette formation s’adresse aux professionnels de l’informatique souhaitant maîtriser les outils et techniques avancés d’administration sous Linux Redhat. Vous apprendrez à gérer efficacement les systèmes Linux, à automatiser les tâches courantes et à résoudre des problématiques complexes grâce à des outils spécifiques.', 'maxresdefault-min.jpg', '4h/jour sur 10 jours', 'RedHat567', '2024-12-30 15:21:46', 'disponible', '2024-12-27', '2025-01-05'),
(2, 'Solutions de virtualisation et Cloud', 2, 'Formation dédiée aux administrateurs systèmes et ingénieurs souhaitant se spécialiser dans les infrastructures Cloud et la virtualisation. Les participants apprendront à concevoir et gérer des environnements Cloud en s\'appuyant sur les meilleures pratiques.', 'virtualization-min.png', '5h/10 jours', 'Cloud555', '2024-12-30 15:23:09', 'indisponible', '2024-12-27', '2025-01-05'),
(3, 'Infrastructure réseau et gestion du stockage', 3, 'Cette formation offre une expertise complète en configuration réseau et en gestion des systèmes de stockage. Idéal pour les professionnels qui souhaitent gérer des infrastructures critiques.', 'gestion-infrastructure-informatique-1656403238-48351-min.jpg', '4h/10 jours', 'Network677', '2024-12-30 15:24:14', 'disponible', '2024-12-27', '2025-01-05'),
(4, 'Protection des données et des systèmes informatiques', 4, 'Cette formation vise à former des professionnels capables d’identifier, prévenir et résoudre des cyberattaques sur les systèmes informatiques.', 'technology-security-concept-safety-digital-protection-system-1080x675-min.jpg', '4h/10 jours', 'CyberSec000', '2024-12-30 15:25:27', 'disponible', '2024-12-27', '2025-01-05');

-- --------------------------------------------------------

--
-- Table structure for table `theme`
--

DROP TABLE IF EXISTS `theme`;
CREATE TABLE IF NOT EXISTS `theme` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `theme`
--

INSERT INTO `theme` (`id`, `nom`) VALUES
(1, 'Linux Redhat'),
(2, 'Architecte Cloud Certifié'),
(3, 'Stockage et Networking'),
(4, 'CyberSécurité');

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `numero_tel` varchar(20) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `type_utilisateur` enum('entreprise','apprenant','formateur','admin') NOT NULL,
  `vue` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `email`, `mot_de_passe`, `numero_tel`, `image`, `type_utilisateur`, `vue`) VALUES
(1, 'test', 'admin@gmail.com', 'f7478165755c262cf8ac92119fabf588', '0561105178', '67862e659a923_images.png', 'admin', 0);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `fk_admin_utilisateur` FOREIGN KEY (`id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `apprenant`
--
ALTER TABLE `apprenant`
  ADD CONSTRAINT `fk_apprenant_utilisateur` FOREIGN KEY (`id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `entreprise`
--
ALTER TABLE `entreprise`
  ADD CONSTRAINT `fk_entreprise_utilisateur` FOREIGN KEY (`id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `formateur`
--
ALTER TABLE `formateur`
  ADD CONSTRAINT `fk_formateur_utilisateur` FOREIGN KEY (`id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
