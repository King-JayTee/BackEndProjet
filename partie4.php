$app->get('/help', function (Request $request, Response $response, $args) {
    $help = "Documentation de l'API:\n- /concepts pour obtenir la liste des concepts\n- /relations pour obtenir la liste des relations\n- /users pour obtenir la liste des utilisateurs\n- /users/create pour créer un utilisateur";
    $response->getBody()->write($help);
    return $response;
});

// Liste des concepts différents dans la base
$app->get('/concepts', function (Request $request, Response $response, $args) {
    $conceptsFr = ["chien", "chat", "voiture", "maison", "arbre", "livre", "ordinateur", "téléphone", "soleil", "lune"];
    $response->getBody()->write(json_encode($conceptsFr));
    return $response->withHeader('Content-Type', 'application/json');
});

// Liste des relations différentes dans la base
$app->get('/relations', function (Request $request, Response $response, $args) {
    $relations = [
        "CapableOf", "UsedFor", "HasA", "IsA", "PartOf",
        "AtLocation", "HasSubevent", "HasFirstSubevent", "HasLastSubevent", "HasPrerequisite",
        "HasProperty", "MotivatedByGoal", "ObstructedBy", "Desires", "CreatedBy",
        "Synonym", "Antonym", "DistinctFrom", "DerivedFrom", "SymbolOf",
        "DefinedAs", "MannerOf", "LocatedNear", "HasContext", "SimilarTo",
        "EtymologicallyRelatedTo", "EtymologicallyDerivedFrom", "CausesDesire", "MadeOf", "ReceivesAction"
    ];
    $response->getBody()->write(json_encode($relations));
    return $response->withHeader('Content-Type', 'application/json');
});

// Liste des utilisateurs de l'application
$app->get('/users', function (Request $request, Response $response, $args) {
    $users = "Liste des utilisateurs (login, score)";  
    $response->getBody()->write($users);
    return $response;
});

// Création d’un utilisateur avec un score nul
$app->post('/users/create', function (Request $request, Response $response, $args) {
    $username = 'your_username';
    $password = 'your_password';  
  
    $response->getBody()->write("Utilisateur $username créé avec succès.");
    return $response;
});

$app->run();
