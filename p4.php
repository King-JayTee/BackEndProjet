<?php
require_once 'config.php'; 

header("Content-Type: application/json");

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        handleGetRequest();
        break;
    case 'POST':
        handlePostRequest();
        break;
    default:
        echo json_encode(["message" => "Méthode non supportée"]);
        break;
}

function handleGetRequest() {
    if ($_GET['path'] === 'concepts') {
        getConcepts();
    } elseif ($_GET['path'] === 'relations') {
        getRelations();
    } elseif ($_GET['path'] === 'users') {
        getUsers();
    } elseif ($_GET['path'] === 'help') {
        getHelp();
    } else {
        echo json_encode(["message" => "Route non reconnue"]);
    }
}

function handlePostRequest() {
    if ($_GET['path'] === 'users') {
        createUser();
    } else {
        echo json_encode(["message" => "Route non reconnue"]);
    }
}

function getConcepts() {
    global $conceptsFr;
    echo json_encode($conceptsFr);
}

function getRelations() {
    global $relations;
    echo json_encode($relations);
}

function getUsers() {
    global $conn;
    $sql = "SELECT login, score FROM users";
    $result = $conn->query($sql);
    $users = [];
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode($users);
}

function createUser() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);
    $sql = "INSERT INTO users (login, password, score) VALUES (?, ?, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $data['login'], $data['password']);
    $stmt->execute();
    echo json_encode(["message" => "Utilisateur créé avec succès"]);
}

function getHelp() {
    $help = [
        "/concepts" => "Retourne la liste des concepts",
        "/relations" => "Retourne la liste des relations",
        "/users" => "Retourne la liste des utilisateurs",
        "/users - POST" => "Crée un utilisateur avec login et mot de passe",
        "/help" => "Retourne ce guide d'utilisation"
    ];
    echo json_encode($help);
}

// Fermer la connexion
$conn->close();
?>
