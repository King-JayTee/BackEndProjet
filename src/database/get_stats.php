<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "conceptnet_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
  // Utiliser les concepts encodés en HTML dans concepts.html au lieu de les récupérer depuis la base de données
}

// 1. Nombre de concepts différents
$sql = "SELECT start_concept AS concept FROM Facts UNION SELECT end_concept AS concept FROM Facts";
$result = $conn->query($sql);

$concepts = [];
while ($row = $result->fetch_assoc()) {
  $concepts[] = $row['concept'];
}
$concepts_count = count(array_unique($concepts));

// 2. Nombre de relations différentes
$sql = "SELECT COUNT(DISTINCT relation) AS count FROM Facts";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$relations_count = $row['count'];

// 3. Nombre de faits dans la base
$sql = "SELECT COUNT(*) AS count FROM Facts";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$facts_count = $row['count'];

// 4. Nombre d’utilisateurs
$sql = "SELECT COUNT(*) AS count FROM users";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$users_count = $row['count'];

$conn->close();

// Renvoie les résultats au format JSON
header('Content-Type: application/json');
echo json_encode([
    'nb_concepts' => $concepts_count,
    'nb_relations' => $relations_count,
    'nb_facts' => $facts_count,
    'nb_users' => $users_count
]);

$conn->close();
