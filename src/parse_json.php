<?php
require_once 'database.php';
// Read the JSON data from a file or API response
$jsonData = file_get_contents('concept_net_data.json');

// Decode the JSON data into a PHP array
$data = json_decode($jsonData, true);

// Initialize an array to store the extracted information
$extractedData = [];

// Loop through each edge in the JSON data
foreach ($data['edges'] as $edge) {
    // Extract the relevant information
    $relationType = $edge['rel']['@id'];
    $startConcept = $edge['start']['label'];
    $endConcept = $edge['end']['label'];
    $weight = $edge['weight'];
    
    // Create an associative array with the extracted information
    $extractedEdge = [
        'relation_type' => $relationType,
        'start_concept' => $startConcept,
        'end_concept' => $endConcept,
        'weight' => $weight
    ];
    
    // Add the extracted edge to the array
    $extractedData[] = $extractedEdge;
}

// Display the extracted data (for testing purposes)
echo "<pre>";
print_r($extractedData);
echo "</pre>";

// Fonction pour récupérer les faits de ConceptNet
function getConceptNetFacts($concept, $lang) {
    $url = "http://api.conceptnet.io/query?node=/c/$lang/$concept&rel=/r/CapableOf&limit=100";
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

// Liste de concepts en français et en anglais
$conceptsFr = ["chien", "chat", "voiture", "maison", "arbre", "livre", "ordinateur", "téléphone", "soleil", "lune"];
$conceptsEn = ["dog", "cat", "car", "house", "tree", "book", "computer", "phone", "sun", "moon"];

// Liste de relations
$relations = [
    "CapableOf", "UsedFor", "HasA", "IsA", "PartOf",
    "AtLocation", "HasSubevent", "HasFirstSubevent", "HasLastSubevent", "HasPrerequisite",
    "HasProperty", "MotivatedByGoal", "ObstructedBy", "Desires", "CreatedBy",
    "Synonym", "Antonym", "DistinctFrom", "DerivedFrom", "SymbolOf",
    "DefinedAs", "MannerOf", "LocatedNear", "HasContext", "SimilarTo",
    "EtymologicallyRelatedTo", "EtymologicallyDerivedFrom", "CausesDesire", "MadeOf", "ReceivesAction"
];

// Récupérer les faits de ConceptNet
$facts = [];
foreach ($conceptsFr as $concept) {
    $facts = array_merge($facts, getConceptNetFacts($concept, "fr"));
}
foreach ($conceptsEn as $concept) {
    $facts = array_merge($facts, getConceptNetFacts($concept, "en"));
}

// Sélectionner 100 faits aléatoires avec au moins 40 concepts différents et 10 relations différentes
$selectedFacts = [];
$selectedConcepts = [];
$selectedRelations = [];

while (count($selectedFacts) < 100 || count($selectedConcepts) < 40 || count($selectedRelations) < 10) {
    $fact = $facts[array_rand($facts)];
    if (!in_array($fact, $selectedFacts)) {
        $selectedFacts[] = $fact;
        $selectedConcepts[] = $fact[0];
        $selectedConcepts[] = $fact[2];
        $selectedRelations[] = $fact[1];
        $selectedConcepts = array_unique($selectedConcepts);
        $selectedRelations = array_unique($selectedRelations);
    }
}

// Inclure le fichier database.php
require_once 'database.php';

// Insérer les faits sélectionnés dans la base de données
insertExtractedData($selectedFacts, $connection);

// Fermer la connexion à la base de données
closeConnection($connection);
?>