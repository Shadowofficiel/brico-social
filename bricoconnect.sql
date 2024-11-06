-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 06 nov. 2024 à 23:17
-- Version du serveur : 8.3.0
-- Version de PHP : 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bricoconnect`
--

-- --------------------------------------------------------

--
-- Structure de la table `amities`
--

DROP TABLE IF EXISTS `amities`;
CREATE TABLE IF NOT EXISTS `amities` (
  `amitie_id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int DEFAULT NULL,
  `utilisateur_ami_id` int DEFAULT NULL,
  `statut` tinyint(1) DEFAULT NULL,
  `date_demande` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`amitie_id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `utilisateur_ami_id` (`utilisateur_ami_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `amities`
--

INSERT INTO `amities` (`amitie_id`, `utilisateur_id`, `utilisateur_ami_id`, `statut`, `date_demande`) VALUES
(4, 7, 6, 1, '2024-11-01 00:56:52'),
(5, 6, 4, 1, '2024-11-01 01:00:03'),
(6, 8, 7, 1, '2024-11-01 20:23:54'),
(9, 6, 8, 1, '2024-11-01 23:42:49'),
(8, 7, 6, 1, '2024-11-01 20:25:35'),
(10, 8, 9, 1, '2024-11-01 23:42:55');

-- --------------------------------------------------------

--
-- Structure de la table `commentaires`
--

DROP TABLE IF EXISTS `commentaires`;
CREATE TABLE IF NOT EXISTS `commentaires` (
  `commentaire_id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int DEFAULT NULL,
  `publication_id` int DEFAULT NULL,
  `contenu` text NOT NULL,
  `date_commentaire` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `parent_id` int DEFAULT NULL,
  PRIMARY KEY (`commentaire_id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `publication_id` (`publication_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `commentaires`
--

INSERT INTO `commentaires` (`commentaire_id`, `utilisateur_id`, `publication_id`, `contenu`, `date_commentaire`, `parent_id`) VALUES
(1, 1, 3, 'bonjout', '2024-10-31 10:17:51', NULL),
(2, 1, 7, 'papa', '2024-10-31 10:38:24', NULL),
(3, 1, 1, 'Bonjour ', '2024-10-31 11:14:02', NULL),
(4, 6, 15, 'tiens', '2024-11-01 02:04:59', NULL),
(5, 6, 15, 'tiens', '2024-11-01 02:05:13', NULL),
(6, 7, 17, 'Il est vilain dh ', '2024-11-01 02:26:02', NULL),
(7, 7, 15, 'Elle ', '2024-11-01 02:31:58', NULL),
(8, 6, 15, 'bete ', '2024-11-01 03:04:02', 7),
(9, 6, 15, 'tu es beaucoup stupide', '2024-11-01 03:08:12', 7),
(10, 7, 15, 'Toi même tu est bête aussi ', '2024-11-01 03:08:29', 5),
(11, 6, 18, 'je veux pas de commentaire', '2024-11-01 03:16:55', 0),
(12, 8, 15, 'Vilain ', '2024-11-01 20:23:43', 0),
(13, 7, 19, 'Laisse moi tranquille 🤣', '2024-11-01 23:32:17', 0),
(14, 6, 19, 'ahy man ? ', '2024-11-01 23:32:45', 13),
(15, 8, 15, 'Chien ', '2024-11-01 23:54:14', 0),
(16, 11, 19, 'mdrrr', '2024-11-06 01:22:46', 0);

-- --------------------------------------------------------

--
-- Structure de la table `mentions_j_aime`
--

DROP TABLE IF EXISTS `mentions_j_aime`;
CREATE TABLE IF NOT EXISTS `mentions_j_aime` (
  `jaime_id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int DEFAULT NULL,
  `publication_id` int DEFAULT NULL,
  `date_jaime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`jaime_id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `publication_id` (`publication_id`)
) ENGINE=MyISAM AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `mentions_j_aime`
--

INSERT INTO `mentions_j_aime` (`jaime_id`, `utilisateur_id`, `publication_id`, `date_jaime`) VALUES
(1, 1, 3, '2024-10-31 10:17:37'),
(2, 1, 7, '2024-10-31 10:38:14'),
(3, 1, 6, '2024-10-31 10:38:38'),
(4, 1, 1, '2024-10-31 10:38:44'),
(5, 1, 4, '2024-10-31 11:16:27'),
(6, 1, 2, '2024-10-31 11:20:22'),
(7, 1, 9, '2024-10-31 11:25:11'),
(8, 1, 12, '2024-10-31 11:27:49'),
(9, 1, 13, '2024-10-31 11:52:00'),
(10, 3, 14, '2024-10-31 23:13:09'),
(11, 6, 15, '2024-10-31 23:44:50'),
(50, 7, 16, '2024-11-01 23:31:16'),
(13, 6, 17, '2024-11-01 02:24:00'),
(58, 6, 18, '2024-11-05 23:43:16'),
(49, 7, 17, '2024-11-01 23:31:13'),
(16, 8, 18, '2024-11-01 20:23:18'),
(51, 7, 15, '2024-11-01 23:31:21'),
(54, 8, 16, '2024-11-01 23:54:07'),
(57, 6, 19, '2024-11-05 23:43:14'),
(55, 8, 15, '2024-11-01 23:54:08'),
(61, 11, 18, '2024-11-06 23:07:53');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `message_id` int NOT NULL AUTO_INCREMENT,
  `expediteur_id` int NOT NULL,
  `destinataire_id` int NOT NULL,
  `contenu` text NOT NULL,
  `fichier` varchar(255) DEFAULT NULL,
  `date_envoi` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `statut` varchar(20) DEFAULT 'pas encore vu',
  PRIMARY KEY (`message_id`),
  KEY `expediteur_id` (`expediteur_id`),
  KEY `destinataire_id` (`destinataire_id`)
) ENGINE=MyISAM AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`message_id`, `expediteur_id`, `destinataire_id`, `contenu`, `fichier`, `date_envoi`, `statut`) VALUES
(65, 6, 7, 'bonour eptit', 'uploads/messages/1730849635_fr hotmail.txt', '2024-11-05 23:33:55', 'vu'),
(64, 6, 7, '', 'uploads/messages/1730849587_prat.png', '2024-11-05 23:33:07', 'vu'),
(63, 6, 7, 'bonkjour', NULL, '2024-11-05 23:32:36', 'vu'),
(66, 7, 8, 'Salut je suis nouveau', 'uploads/messages/1730849666_IMG_8273.jpeg', '2024-11-05 23:34:26', 'pas encore vu'),
(62, 7, 6, '', 'uploads/messages/1730849130_45b1c4c3-be53-4b2c-8f87-6453f2d039da.jpeg', '2024-11-05 23:25:30', 'vu'),
(61, 7, 6, 'Cc', NULL, '2024-11-05 23:25:10', 'vu'),
(59, 7, 6, 'Hyn ?', NULL, '2024-11-05 23:24:51', 'vu'),
(60, 7, 6, 'Pardon ?', NULL, '2024-11-05 23:25:04', 'vu'),
(57, 7, 6, '?', NULL, '2024-11-05 23:17:51', 'vu'),
(58, 6, 7, '', '../uploads/messages/1730848924_con.jpg', '2024-11-05 23:22:04', 'vu'),
(56, 6, 7, '', '../uploads/messages/1730848555_question.jpg', '2024-11-05 23:15:55', 'vu');

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `type` varchar(50) NOT NULL,
  `contenu` text NOT NULL,
  `vue` tinyint(1) DEFAULT '0',
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`notification_id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `utilisateur_id`, `type`, `contenu`, `vue`, `date_creation`) VALUES
(1, 6, 'message', 'Vous avez reçu un message de Shadow', 1, '2024-11-01 02:00:25'),
(2, 7, 'message', 'Vous avez reçu un message de jean', 1, '2024-11-01 02:00:42'),
(3, 7, 'message', 'Vous avez reçu un message de jean', 1, '2024-11-01 02:00:52'),
(4, 7, 'message', 'Vous avez reçu un message de jean', 1, '2024-11-01 02:04:35'),
(5, 7, 'invitation', 'Vous avez reçu une demande d\'ami de Tonton Bernard ', 1, '2024-11-01 20:23:54'),
(6, 6, 'invitation', 'Vous avez reçu une demande d\'ami de Tonton Bernard ', 1, '2024-11-01 20:24:07'),
(7, 8, 'message', 'Vous avez reçu un message de Shadow', 1, '2024-11-01 20:25:57'),
(8, 6, 'message', 'Vous avez reçu un message de Shadow', 1, '2024-11-01 20:31:24'),
(9, 6, 'message', 'Vous avez reçu un message de Shadow', 1, '2024-11-01 21:56:37'),
(10, 6, 'message', 'Vous avez reçu un message de Shadow', 1, '2024-11-01 21:56:46'),
(11, 6, 'message', 'Vous avez reçu un message de Shadow', 1, '2024-11-01 21:57:08'),
(12, 6, 'message', 'Vous avez reçu un message de Shadow', 1, '2024-11-01 21:57:10'),
(13, 6, 'message', 'Vous avez reçu un message de Shadow', 1, '2024-11-01 21:59:23'),
(14, 6, 'message', 'Vous avez reçu un message de Shadow', 1, '2024-11-01 21:59:38'),
(15, 6, 'message', 'Vous avez reçu un message de Shadow', 1, '2024-11-01 22:06:50'),
(16, 6, 'message', 'Vous avez reçu un message de Shadow', 1, '2024-11-01 22:09:41'),
(17, 6, 'message', 'Vous avez reçu un message de Shadow', 1, '2024-11-01 22:16:26'),
(18, 6, 'message', 'Vous avez reçu un message de Shadow', 1, '2024-11-01 22:24:05'),
(19, 6, 'message', 'Vous avez reçu un message de Shadow', 1, '2024-11-01 22:29:28'),
(20, 6, 'message', 'Vous avez reçu un message de Shadow', 1, '2024-11-01 22:30:24'),
(21, 8, 'message', 'Vous avez reçu un message de Shadow', 1, '2024-11-01 22:30:41'),
(22, 7, 'message', 'Vous avez reçu un message de jean', 1, '2024-11-01 22:38:12'),
(23, 6, 'message', 'Vous avez reçu un message de Shadow', 1, '2024-11-01 22:38:29'),
(24, 7, 'message', 'Vous avez reçu un message de jean', 1, '2024-11-01 22:38:49'),
(25, 7, 'message', 'Vous avez reçu un message de jean', 1, '2024-11-01 22:39:13'),
(26, 7, 'message', 'Vous avez reçu un message de Tonton Bernard ', 1, '2024-11-01 23:41:53'),
(27, 8, 'invitation', 'Vous avez reçu une demande d\'ami de jean', 1, '2024-11-01 23:42:49');

-- --------------------------------------------------------

--
-- Structure de la table `partages`
--

DROP TABLE IF EXISTS `partages`;
CREATE TABLE IF NOT EXISTS `partages` (
  `partage_id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int DEFAULT NULL,
  `publication_id` int DEFAULT NULL,
  `date_partage` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`partage_id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `publication_id` (`publication_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `partages`
--

INSERT INTO `partages` (`partage_id`, `utilisateur_id`, `publication_id`, `date_partage`) VALUES
(1, 1, 3, '2024-10-31 10:18:00'),
(2, 2, 6, '2024-10-31 10:25:29'),
(3, 1, 2, '2024-10-31 11:28:27'),
(4, 3, 14, '2024-10-31 23:13:14'),
(5, 7, 15, '2024-11-01 02:34:46'),
(6, 7, 17, '2024-11-01 03:42:53');

-- --------------------------------------------------------

--
-- Structure de la table `publications`
--

DROP TABLE IF EXISTS `publications`;
CREATE TABLE IF NOT EXISTS `publications` (
  `publication_id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int DEFAULT NULL,
  `contenu` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`publication_id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `publications`
--

INSERT INTO `publications` (`publication_id`, `utilisateur_id`, `contenu`, `image`, `date_creation`) VALUES
(18, 6, 'jadore', 'uploads/prat.png', '2024-11-01 03:16:31'),
(17, 7, 'Je dors ', 'uploads/IMG-20230313-WA0025_Original.jpeg', '2024-11-01 02:23:45'),
(16, 6, 'BONJOUR', 'uploads/locust.jpg', '2024-10-31 23:55:34'),
(15, 7, 'Je suis nouveau ', 'uploads/IMG_8201.jpeg', '2024-10-31 23:44:21'),
(14, 3, 'Bonjour ', 'uploads/IMG_8201.jpeg', '2024-10-31 23:03:34'),
(20, 9, 'LELELE', 'uploads/prat.png', '2024-11-06 00:46:37'),
(21, 11, 'BONJOUR', NULL, '2024-11-06 14:04:25');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `utilisateur_id` int NOT NULL AUTO_INCREMENT,
  `nom_utilisateur` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe_hash` varchar(255) NOT NULL,
  `photo_profil` varchar(255) DEFAULT NULL,
  `bio` text,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dernier_login` timestamp NULL DEFAULT NULL,
  `premiere_connexion` tinyint(1) DEFAULT '1',
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiration` datetime DEFAULT NULL,
  PRIMARY KEY (`utilisateur_id`),
  UNIQUE KEY `nom_utilisateur` (`nom_utilisateur`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`utilisateur_id`, `nom_utilisateur`, `email`, `mot_de_passe_hash`, `photo_profil`, `bio`, `date_creation`, `dernier_login`, `premiere_connexion`, `reset_token`, `token_expiration`) VALUES
(7, 'Shadow', 'shadowofficiel001@gmail.com', '$2y$10$QD1gW83qa6VfqHlNQOUW4O3IvJbrEvGLLAe2U4ZUf.u81SNBJpt4i', '../uploads/IMG_8147.jpeg', 'J’aime palabre laisse ça ', '2024-10-31 23:43:32', NULL, 0, NULL, NULL),
(6, 'jean', 'jeanpierre444@icloud.com', '$2y$10$C.HFAkvmniJ1uSmTjjptkeXtLJz06GZ89Gug43ezAXzOJ7UN8qlCy', '../uploads/eli.png', 'jaime baiser', '2024-10-31 23:43:05', NULL, 0, NULL, NULL),
(8, 'Tonton Bernard ', 'tonton@gmail.com', '$2y$10$sCOeMpqawQeP84sWFBkkSu0/j0.Oy9uqhYWY8EhL0ael4hyRbsEv.', '../images/default_avatar.png', 'Je suis le tonton des tontons ', '2024-11-01 20:22:00', NULL, 0, NULL, NULL),
(11, 'test', 'shadowofficiel100@gmail.com', '$2y$10$oDRwQK8fug5fNvMsRlGnKOX2UFIKsztF3tDaC2E63f.PafVtHCw9O', '../uploads/dalli.jpg', 'appelez moi lo gros', '2024-11-06 01:21:19', NULL, 1, NULL, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
