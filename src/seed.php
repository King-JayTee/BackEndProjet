<?php

function getConceptNetFacts($concept, $lang)
{
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

function getInitialFacts()
{
    $concepts = [
        "dog", "cat", "house", "tree", "sun", "lune", "computer", "chair",
        "table", "phone", "pen", "book", "flower", "water", "music", "food",
        "work", "job", "money", "time", "family", "health", "sport", "game",
        "movie", "art", "science", "nature", "travel", "holiday", "end"
    ];

    $initialFacts = [];
    foreach ($concepts as $concept) {
        $initialFacts = array_merge($initialFacts, getConceptNetFacts($concept, "en"));
    }
    print_r($initialFacts);
    return $initialFacts;
}


function generateConceptsHTML($initialFacts)
{
    $html = "<table border='1'>";
    $html .= "<tr><th>Start</th><th>Relation</th><th>End</th></tr>";
    foreach ($initialFacts as $fact) {
        $html .= "<tr><td>" . htmlspecialchars($fact[0]) . "</td><td>" . htmlspecialchars($fact[1]) . "</td><td>" . htmlspecialchars($fact[2]) . "</td></tr>";
    }
    $html .= "</table>";

    $file = "../public/templates/concepts.html";
    file_put_contents($file, $html);
}

$initialFacts = getInitialFacts();
generateConceptsHTML($initialFacts);