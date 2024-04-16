<?php
// Database connection details
$host = 'localhost';
$username = 'your_username';
$password = 'your_password';
$database = 'your_database_name';

// Create a connection to the database
$connection = new mysqli($host, $username, $password, $database);

function createConceptNetDataTable($connection) {
    $sql = "CREATE TABLE IF NOT EXISTS concept_net_data (
        id INT AUTO_INCREMENT PRIMARY KEY,
        relation_type VARCHAR(255),
        start_concept VARCHAR(255),
        end_concept VARCHAR(255),
        weight DECIMAL(10, 2)
    )";
    
    if ($connection->query($sql) === TRUE) {
        echo "Table concept_net_data created successfully";
    } else {
        echo "Error creating table: " . $connection->error;
    }
}
-- insert_data.sql

INSERT INTO ConceptNetRelations (relation_uri, description, examples) VALUES 
('/r/RelatedTo', 'The most general relation. There is some positive relationship between A and B.', 'learn ↔ erudition'),
('/r/FormOf', 'A is an inflected form of B; B is the root word of A.', 'slept → sleep'),
('/r/IsA', 'A is a subtype or a specific instance of B.', 'car → vehicle; Chicago → city'),
('/r/PartOf', 'A is a part of B.', 'gearshift → car'),
('/r/HasA', 'B belongs to A, either as an inherent part or due to a social construct of possession.', 'bird → wing; pen → ink'),
('/r/UsedFor', 'A is used for B; the purpose of A is B.', 'bridge → cross water'),
('/r/CapableOf', 'Something that A can typically do is B.', 'knife → cut'),
('/r/AtLocation', 'A is a typical location for B, or A is the inherent location of B.', 'butter → refrigerator; Boston → Massachusetts'),
('/r/Causes', 'A and B are events, and it is typical for A to cause B.', 'exercise → sweat'),
('/r/HasSubevent', 'A and B are events, and B happens as a subevent of A.', 'eating → chewing'),
('/r/HasFirstSubevent', 'A is an event that begins with subevent B.', 'sleep → close eyes'),
('/r/HasLastSubevent', 'A is an event that concludes with subevent B.', 'cook → clean up kitchen'),
('/r/HasPrerequisite', 'In order for A to happen, B needs to happen; B is a dependency of A.', 'dream → sleep'),
('/r/HasProperty', 'A has B as a property; A can be described as B.', 'ice → cold'),
('/r/MotivatedByGoal', 'Someone does A because they want result B; A is a step toward accomplishing the goal B.', 'compete → win'),
('/r/ObstructedBy', 'A is a goal that can be prevented by B; B is an obstacle in the way of A.', 'sleep → noise'),
('/r/Desires', 'A is a conscious entity that typically wants B.', 'person → love'),
('/r/CreatedBy', 'B is a process or agent that creates A.', 'cake → bake'),
('/r/Synonym', 'A and B have very similar meanings.', 'sunlight ↔ sunshine'),
('/r/Antonym', 'A and B are opposites in some relevant way.', 'black ↔ white; hot ↔ cold'),
('/r/DistinctFrom', 'A and B are distinct member of a set; something that is A is not B.', 'red ↔ blue; August ↔ September'),
('/r/DerivedFrom', 'A is a word or phrase that appears within B and contributes to B''s meaning.', 'pocketbook → book'),
('/r/SymbolOf', 'A symbolically represents B.', 'red → fervor'),
('/r/DefinedAs', 'A and B overlap considerably in meaning, and B is a more explanatory version of A.', 'peace → absence of war'),
('/r/MannerOf', 'A is a specific way to do B. Similar to "IsA", but for verbs.', 'auction → sale'),
('/r/LocatedNear', 'A and B are typically found near each other.', 'chair ↔ table'),
('/r/HasContext', 'A is a word used in the context of B.', 'astern → ship; arvo → Australia'),
('/r/SimilarTo', 'A is similar to B.', 'mixer ↔ food processor'),
('/r/EtymologicallyRelatedTo', 'A and B have a common origin.', 'folkmusiikki ↔ folk music'),
('/r/EtymologicallyDerivedFrom', 'A is derived from B.', 'dejta → date'),
('/r/CausesDesire', 'A makes someone want B.', 'having no food → go to a store'),
('/r/MadeOf', 'A is made of B.', 'bottle → plastic'),
('/r/ReceivesAction', 'B can be done to A.', 'button → push'),
('/r/ExternalURL', 'Instead of relating to ConceptNet nodes, this pseudo-relation points to a URL outside of ConceptNet.', 'knowledge → http://dbpedia.org/page/Knowledge');

// Check if the connection was successful
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Function to insert extracted data into the database
function insertExtractedData($extractedData, $connection) {
    // Prepare the SQL statement
    $sql = "INSERT INTO concept_net_data (relation_type, start_concept, end_concept, weight) VALUES (?, ?, ?, ?)";
    
    // Create a prepared statement
    $statement = $connection->prepare($sql);
    
    // Loop through each extracted edge and insert into the database
    foreach ($extractedData as $edge) {
        $relationType = $edge['relation_type'];
        $startConcept = $edge['start_concept'];
        $endConcept = $edge['end_concept'];
        $weight = $edge['weight'];
        
        // Bind the parameters to the prepared statement
        $statement->bind_param("sssd", $relationType, $startConcept, $endConcept, $weight);
        
        // Execute the statement
        $statement->execute();
    }
    
    // Close the prepared statement
    $statement->close();
}

// Function to close the database connection
function closeConnection($connection) {
    $connection->close();
}
?>