-- Création de la base de données
CREATE DATABASE IF NOT EXISTS {{databaseName}};

-- Utilisation de la base de données
USE {{databaseName}};

-- Création de la table "taches"
CREATE TABLE IF NOT EXISTS taches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255),
    description TEXT,
    dateEcheance DATE,
    statut VARCHAR(50)
);