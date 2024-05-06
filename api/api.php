<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: access, Content-Type, Authorization');
header("Access-Control-Allow-Credentials: true");

$requestPath = trim($_SERVER['REQUEST_URI'], '/');
$method = $_SERVER['REQUEST_METHOD'];

switch ($requestPath) {
    case 'concepts':
        if ($method == 'GET') {
            getConcepts($mysqli);
        }
        break;
    case 'relations':
        if ($method == 'GET') {
            getRelations($mysqli);
        }
        break;
    case 'users':
        if ($method == 'GET') {
            getUsers($mysqli);
        }
        break;
    case 'users/create':
        if ($method == 'POST') {
            createUser($mysqli);
        }
        break;
    case 'help':
        if ($method == 'GET') {
            getHelp();
        }
        break;
    default:
        echo json_encode(['message' => '404 Not Found']);
}

function getConcepts($mysqli)
{
    $result = $mysqli->query('SELECT DISTINCT start_concept, end_concept FROM relations');
    $concepts = [];
    while ($row = $result->fetch_assoc()) {
        $concepts[] = $row['start_concept'];
        $concepts[] = $row['end_concept'];
    }
    echo json_encode(array_unique($concepts));
}

function getRelations($mysqli)
{
    $result = $mysqli->query('SELECT DISTINCT relation FROM relations');
    $relations = [];
    while ($row = $result->fetch_assoc()) {
        $relations[] = $row;
    }
    echo json_encode($relations);
}

function getUsers($mysqli)
{
    $result = $mysqli->query('SELECT username, score FROM users');
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode($users);
}

function createUser($mysqli)
{
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $query = "INSERT INTO users (username, password, score) VALUES ('$username', '$password', 0)";
    if ($mysqli->query($query) === true) {
        echo json_encode(['message' => 'User created successfully']);
    } else {
        echo json_encode(['message' => 'Error: ' . $mysqli->error]);
    }
}

function getHelp()
{
    $documentation = [
        'endpoints' => [
            '/concepts' => [
                'method' => 'GET',
                'description' => 'Retrieves a list of all unique concepts from the database.',
                'example_request' => 'GET http://yourdomain.com/concepts'
            ],
            '/relations' => [
                'method' => 'GET',
                'description' => 'Retrieves a list of all relations from the database.',
                'example_request' => 'GET http://yourdomain.com/relations'
            ],
            '/users' => [
                'method' => 'GET',
                'description' => 'Retrieves a list of all users with their username and score.',
                'example_request' => 'GET http://yourdomain.com/users'
            ],
            '/users/create' => [
                'method' => 'POST',
                'description' => 'Creates a new user with a zero score. Requires username and password as POST parameters.',
                'example_request' => 'POST http://yourdomain.com/users/create',
                'parameters' => [
                    'username' => 'Desired username',
                    'password' => 'Desired password'
                ]
            ]
        ],
        'more_info' => 'For more details, refer to the full API documentation or contact the admin.'
    ];
    echo json_encode($documentation, JSON_PRETTY_PRINT);
}
