<?php
require_once "../seed.php";

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "conceptnet_db";

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === true) {
    echo "La base de données a été créée avec succès ou existe déjà.<br>";
} else {
    echo "Erreur lors de la création de la base de données : " . $conn->error . "\n";
}

$conn->select_db($dbname);

$sql = "CREATE TABLE IF NOT EXISTS Facts (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    start_concept VARCHAR(255) NOT NULL,
    relation VARCHAR(255) NOT NULL,
    end_concept VARCHAR(255) NOT NULL
)";

if ($conn->query($sql) === true) {
    echo "La table 'Facts' a été créée avec succès ou existe déjà.<br>";
} else {
    echo "Erreur lors de la création de la table 'facts' : " . $conn->error;
}

$sql = "CREATE TABLE IF NOT EXISTS Users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL
  )";
  
  if ($conn->query($sql) === true) {
      echo "Table users created successfully";
  } else {
      echo "Error creating table: " . $conn->error;
  }

$initialFacts = getInitialFacts();

foreach ($initialFacts as $fact) {
    $start_concept = mysqli_real_escape_string($conn, $fact[0]);
    $relation = mysqli_real_escape_string($conn, $fact[1]);
    $end_concept = mysqli_real_escape_string($conn, $fact[2]);

    $sql = "INSERT INTO Facts (start_concept, relation, end_concept)
    VALUES ('$start_concept', '$relation', '$end_concept')";

    if ($conn->query($sql) === false) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
