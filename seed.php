<?php
// Inclure le fichier database.php
require_once 'database.php';

// Fonction pour récupérer les faits de ConceptNet
function getConceptNetFacts($concept, $lang) {
    $url = "http://api.conceptnet.io/query?node=/c/$lang/$concept&rel=/r/CapableOf&limit=10";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    $facts = [];
    foreach ($data["edges"] as $edge) {
        $start = $edge["start"]["label"];
        $relation = $edge["rel"]["label"];
        $end = $edge["end"]["label"];
        $facts[] = [$start, $relation, $end];
    }
    return $facts;
}

// Liste de concepts pour le seeding initial
$concepts = ["dog", "cat", "house", "tree", "sun", "lune", "voiture", "livre"];

// Récupérer les faits de ConceptNet pour chaque concept
$initialFacts = [];
foreach ($concepts as $concept) {
    $initialFacts = array_merge($initialFacts, getConceptNetFacts($concept, "en"));
    $initialFacts = array_merge($initialFacts, getConceptNetFacts($concept, "fr"));
}

// Insérer les faits initiaux dans la base de données
insertExtractedData($initialFacts, $connection);

// Fermer la connexion à la base de données
closeConnection($connection);

echo "Les faits initiaux ont été insérés dans la base de données.";
?>