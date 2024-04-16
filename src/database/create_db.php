<?php
// Informations de connexion à la base de données
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "conceptnet_db";

// Créer une connexion
$conn = new mysqli($servername, $username, $password);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Créer la base de données
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "La base de données a été créée avec succès ou existe déjà.<br>";
} else {
    echo "Erreur lors de la création de la base de données : " . $conn->error;
}

// Sélectionner la base de données
$conn->select_db($dbname);

// Créer la table pour stocker les faits
$sql = "CREATE TABLE IF NOT EXISTS facts (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    start_concept VARCHAR(255) NOT NULL,
    relation VARCHAR(255) NOT NULL,
    end_concept VARCHAR(255) NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "La table 'facts' a été créée avec succès ou existe déjà.<br>";
} else {
    echo "Erreur lors de la création de la table 'facts' : " . $conn->error;
}

// Fermer la connexion
$conn->close();
?>