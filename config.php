<?php

// Paramètres de connexion à la base de données
$host = 'localhost'; // Par exemple, 'localhost' si la base de données est sur le même serveur
$utilisateur = 'root';
$motDePasse = 'root';
$nomBaseDeDonnees = 'gestionTaches';

// Chaîne de connexion PDO pour la connexion initiale (sans sélection de base de données)
$dsn = "mysql:host=$host;charset=utf8mb4";

// Options PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Tentative de connexion à MySQL sans sélection de base de données
    $connexion = new PDO($dsn, $utilisateur, $motDePasse, $options);

    // Vérifier si la base de données existe
    $query = $connexion->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$nomBaseDeDonnees'");
    $databaseExists = ($query->rowCount() > 0);

    // Si la base de données n'existe pas, la créer
    if (!$databaseExists) {
        $connexion->exec("CREATE DATABASE $nomBaseDeDonnees");
        echo "Base de données créée avec succès.\n";
    }

    // Sélection de la base de données
    $dsn = "mysql:host=$host;dbname=$nomBaseDeDonnees;charset=utf8mb4";
    $connexion = new PDO($dsn, $utilisateur, $motDePasse, $options);

    // Afficher un message de réussite
    echo "Connexion à la base de données réussie.\n";
} catch (PDOException $e) {
    // Afficher un message d'échec
    die("Échec de la connexion à la base de données : " . $e->getMessage() . "\n");
}