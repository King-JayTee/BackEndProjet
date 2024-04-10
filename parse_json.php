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

// TODO: Insert the extracted data into the database
insertExtractedData($extractedData, $connection);


closeConnection($connection);
?>