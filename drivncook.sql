-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 05 jan. 2026 à 05:55
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `drivncook`
--

-- --------------------------------------------------------

--
-- Structure de la table `camions`
--

CREATE TABLE `camions` (
  `id` int(11) NOT NULL,
  `reference` varchar(50) DEFAULT NULL,
  `etat` enum('DISPONIBLE','ATTRIBUE','PANNE') DEFAULT 'DISPONIBLE',
  `franchise_id` int(11) DEFAULT NULL,
  `localisation` varchar(100) DEFAULT NULL,
  `etat_technique` enum('OPERATIONNEL','PANNE') DEFAULT 'OPERATIONNEL'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `camions`
--

INSERT INTO `camions` (`id`, `reference`, `etat`, `franchise_id`, `localisation`, `etat_technique`) VALUES
(4, 'Camion 3', 'ATTRIBUE', 6, 'Orsay', 'PANNE'),
(5, 'Camion 1', 'DISPONIBLE', NULL, 'Versailles', 'OPERATIONNEL'),
(6, 'Camions 2 ', 'DISPONIBLE', NULL, 'Montreuil', 'OPERATIONNEL'),
(8, 'Camion 4 ', 'DISPONIBLE', NULL, 'Nanterre', 'OPERATIONNEL');

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(11) NOT NULL,
  `franchise_id` int(11) DEFAULT NULL,
  `date_commande` date DEFAULT NULL,
  `total` float DEFAULT NULL,
  `taux_dc` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id`, `franchise_id`, `date_commande`, `total`, `taux_dc`) VALUES
(1, 6, '2026-01-03', NULL, 80),
(2, 6, '2026-01-03', NULL, 85),
(3, 6, '2026-01-03', NULL, 85.71),
(4, 6, '2026-01-03', NULL, 95.45),
(5, 6, '2026-01-03', NULL, 90.91),
(6, 6, '2026-01-03', NULL, 87.5),
(7, 6, '2026-01-03', NULL, 91.67),
(8, 6, '2026-01-03', NULL, 87.5),
(9, 6, '2026-01-03', NULL, 90),
(10, 6, '2026-01-03', NULL, 100),
(11, 6, '2026-01-03', NULL, 89.47),
(12, 6, '2026-01-03', 18, 100),
(13, 6, '2026-01-04', 6, 100),
(14, 6, '2026-01-05', 10, 100),
(15, 6, '2026-01-05', 4.5, 100),
(16, 6, '2026-01-05', 2.5, 100);

-- --------------------------------------------------------

--
-- Structure de la table `commande_lignes`
--

CREATE TABLE `commande_lignes` (
  `id` int(11) NOT NULL,
  `commande_id` int(11) NOT NULL,
  `produit_id` int(11) DEFAULT NULL,
  `quantite` int(11) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `type` enum('DC','LIBRE') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `commande_lignes`
--

INSERT INTO `commande_lignes` (`id`, `commande_id`, `produit_id`, `quantite`, `prix`, `type`) VALUES
(1, 12, 24, 6, 3.00, 'DC'),
(2, 13, 25, 4, 1.50, 'DC'),
(3, 14, 26, 4, 2.50, 'DC'),
(4, 15, 26, 1, 2.50, 'DC'),
(5, 15, 27, 1, 2.00, 'DC'),
(6, 16, 26, 1, 2.50, 'DC');

-- --------------------------------------------------------

--
-- Structure de la table `entrepots`
--

CREATE TABLE `entrepots` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `localisation` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `entrepots`
--

INSERT INTO `entrepots` (`id`, `nom`, `localisation`) VALUES
(1, 'Entrepôt Paris', 'Paris'),
(2, 'Entrepôt Créteil', 'Créteil'),
(3, 'Entrepôt Nanterre', 'Nanterre'),
(4, 'Entrepôt Versailles', 'Versailles');

-- --------------------------------------------------------

--
-- Structure de la table `franchises`
--

CREATE TABLE `franchises` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `date_entree` date DEFAULT NULL,
  `droit_entree_paye` tinyint(1) DEFAULT 0,
  `actif` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `franchises`
--

INSERT INTO `franchises` (`id`, `nom`, `email`, `password`, `date_entree`, `droit_entree_paye`, `actif`) VALUES
(1, 'Chaudemanche', 'matis.chaudemanche@gmail.com', '$2y$10$VIgaCsUE5ToK8EdC1Z1kneXiHS15WJCGl5ptYU0NIkb5Jn4tQ472W', '2026-01-02', 0, 0),
(6, 'Franchise1', 'franchise1@dc.fr', NULL, NULL, 0, 1),
(7, 'Franchise2', 'franchise2@dc.fr', NULL, NULL, 0, 1),
(9, 'Franchise3', 'Franchise3@gmail.com', NULL, NULL, 0, 1),
(11, 'TEST', 'test@gmail.com', NULL, NULL, 0, 0),
(12, 'FINAL', 'final@gmail.com', NULL, NULL, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

CREATE TABLE `paiements` (
  `id` int(11) NOT NULL,
  `franchise_id` int(11) DEFAULT NULL,
  `type` enum('DROIT_ENTREE','REDEVANCE') DEFAULT NULL,
  `montant` float DEFAULT NULL,
  `date_paiement` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `paiements`
--

INSERT INTO `paiements` (`id`, `franchise_id`, `type`, `montant`, `date_paiement`) VALUES
(1, 1, 'DROIT_ENTREE', 50000, '2026-01-02'),
(2, 1, 'DROIT_ENTREE', 50000, '2026-01-02'),
(3, 1, 'DROIT_ENTREE', 50000, '2026-01-02'),
(4, 1, 'DROIT_ENTREE', 50000, '2026-01-02'),
(5, 1, 'DROIT_ENTREE', 50000, '2026-01-02'),
(6, 1, 'DROIT_ENTREE', 50000, '2026-01-02'),
(7, 1, 'DROIT_ENTREE', 50000, '2026-01-02'),
(8, 1, 'DROIT_ENTREE', 50000, '2026-01-02'),
(9, 1, 'DROIT_ENTREE', 50000, '2026-01-02'),
(10, 1, 'DROIT_ENTREE', 50000, '2026-01-02'),
(11, 1, 'DROIT_ENTREE', 50000, '2026-01-02'),
(12, 1, 'DROIT_ENTREE', 50000, '2026-01-02'),
(13, 1, 'DROIT_ENTREE', 50000, '2026-01-02'),
(14, 1, 'DROIT_ENTREE', 50000, '2026-01-02'),
(15, 6, 'DROIT_ENTREE', 50000, '2026-01-03'),
(16, 11, 'DROIT_ENTREE', 50000, '2026-01-03'),
(17, 12, 'DROIT_ENTREE', 50000, '2026-01-05');

-- --------------------------------------------------------

--
-- Structure de la table `pannes`
--

CREATE TABLE `pannes` (
  `id` int(11) NOT NULL,
  `camion_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `date_panne` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `pannes`
--

INSERT INTO `pannes` (`id`, `camion_id`, `type`, `date_panne`) VALUES
(1, 4, 'Freins', '2026-01-03 06:40:25'),
(2, 4, 'Freins', '2026-01-03 06:40:32'),
(3, 4, 'Refroidissement', '2026-01-03 06:50:56'),
(4, 4, 'Refroidissement', '2026-01-04 01:20:20'),
(5, 4, 'Refroidissement', '2026-01-04 03:31:25'),
(6, 4, 'Carburant', '2026-01-04 16:06:35');

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `entrepot_id` int(11) DEFAULT NULL,
  `origine` enum('DC','LIBRE') DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `prix` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `nom`, `entrepot_id`, `origine`, `stock`, `prix`) VALUES
(19, 'Lait', 1, 'DC', 0, 0.00),
(20, 'Jus de mangue ', 3, 'DC', 0, 0.00),
(21, 'Lassagne', 2, 'DC', 0, 0.00),
(22, 'Fromage', 4, 'DC', 0, 0.00),
(25, 'coca cola ', 3, 'DC', 2, 1.50),
(26, 'poivrons', 1, 'DC', 14, 2.50),
(27, 'Chips', 4, 'DC', 21, 2.00);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('ADMIN','FRANCHISE') NOT NULL,
  `franchise_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `franchise_id`) VALUES
(1, 'admin@drivncook.fr', 'admin', 'ADMIN', NULL),
(2, 'franchise1@dc.fr', 'test', 'FRANCHISE', 6),
(3, 'franchise2@dc.fr', 'test', 'FRANCHISE', 7),
(4, 'Franchise3@gmail.com', 'test', 'FRANCHISE', 9),
(6, 'test@gmail.com', 'test', 'FRANCHISE', 11),
(7, 'final@gmail.com', 'test', 'FRANCHISE', 12);

-- --------------------------------------------------------

--
-- Structure de la table `ventes`
--

CREATE TABLE `ventes` (
  `id` int(11) NOT NULL,
  `franchise_id` int(11) DEFAULT NULL,
  `date_vente` date DEFAULT NULL,
  `montant` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `ventes`
--

INSERT INTO `ventes` (`id`, `franchise_id`, `date_vente`, `montant`) VALUES
(1, 6, '2026-01-03', 8000),
(2, 6, '2026-01-03', 5000),
(3, 6, '2026-01-05', 1000),
(4, 6, '2026-01-05', 6000),
(5, 6, '2026-01-05', 6000);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `camions`
--
ALTER TABLE `camions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_camion_franchise` (`franchise_id`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `franchise_id` (`franchise_id`);

--
-- Index pour la table `commande_lignes`
--
ALTER TABLE `commande_lignes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `commande_id` (`commande_id`);

--
-- Index pour la table `entrepots`
--
ALTER TABLE `entrepots`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `franchises`
--
ALTER TABLE `franchises`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `franchise_id` (`franchise_id`);

--
-- Index pour la table `pannes`
--
ALTER TABLE `pannes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `camion_id` (`camion_id`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entrepot_id` (`entrepot_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `ventes`
--
ALTER TABLE `ventes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `franchise_id` (`franchise_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `camions`
--
ALTER TABLE `camions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `commande_lignes`
--
ALTER TABLE `commande_lignes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `entrepots`
--
ALTER TABLE `entrepots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `franchises`
--
ALTER TABLE `franchises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `paiements`
--
ALTER TABLE `paiements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `pannes`
--
ALTER TABLE `pannes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `ventes`
--
ALTER TABLE `ventes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `camions`
--
ALTER TABLE `camions`
  ADD CONSTRAINT `fk_camion_franchise` FOREIGN KEY (`franchise_id`) REFERENCES `franchises` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`franchise_id`) REFERENCES `franchises` (`id`);

--
-- Contraintes pour la table `commande_lignes`
--
ALTER TABLE `commande_lignes`
  ADD CONSTRAINT `commande_lignes_ibfk_1` FOREIGN KEY (`commande_id`) REFERENCES `commandes` (`id`);

--
-- Contraintes pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD CONSTRAINT `paiements_ibfk_1` FOREIGN KEY (`franchise_id`) REFERENCES `franchises` (`id`);

--
-- Contraintes pour la table `pannes`
--
ALTER TABLE `pannes`
  ADD CONSTRAINT `pannes_ibfk_1` FOREIGN KEY (`camion_id`) REFERENCES `camions` (`id`);

--
-- Contraintes pour la table `produits`
--
ALTER TABLE `produits`
  ADD CONSTRAINT `produits_ibfk_1` FOREIGN KEY (`entrepot_id`) REFERENCES `entrepots` (`id`);

--
-- Contraintes pour la table `ventes`
--
ALTER TABLE `ventes`
  ADD CONSTRAINT `ventes_ibfk_1` FOREIGN KEY (`franchise_id`) REFERENCES `franchises` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
