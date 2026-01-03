CREATE DATABASE drivncook;
USE drivncook;

-- UTILISATEURS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100),
    password VARCHAR(255),
    role ENUM('admin','franchisé')
);

-- FRANCHISÉS
CREATE TABLE franchises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    email VARCHAR(100),
    date_entree DATE,
    chiffre_affaires DECIMAL(10,2)
);

-- CAMIONS
CREATE TABLE camions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(50),
    statut ENUM('service','panne','maintenance'),
    franchise_id INT
);

-- ENTREPÔTS
CREATE TABLE entrepots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    ville VARCHAR(100)
);

-- PRODUITS
CREATE TABLE produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    prix DECIMAL(6,2),
    origine ENUM('drivncook','libre')
);

-- COMMANDES
CREATE TABLE commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    franchise_id INT,
    total DECIMAL(10,2),
    part_drivncook DECIMAL(10,2),
    date_commande DATE
);

-- VENTES
CREATE TABLE ventes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    franchise_id INT,
    montant DECIMAL(10,2),
    date_vente DATE
);
