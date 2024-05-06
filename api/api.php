<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: access, Content-Type, Authorization');
header("Access-Control-Allow-Credentials: true");

$dbHost = 'localhost';
$dbName = 'conceptnet_db';
$dbUsername = 'localhost';
$dbPassword = '';

$mysqli = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
} else {
    echo "Connected successfully";
}

$app->get('concepts', function () use ($mysqli) {
    $result = $mysqli->query('SELECT DISTINCT start_concept, end_concept FROM relations');
    $concepts = [];
    while ($row = $result->fetch_assoc()) {
        $concepts[] = $row['start_concept'];
        $concepts[] = $row['end_concept'];
    }
    echo json_encode(array_unique($concepts));
});


$app->get('/relations', function () use ($mysqli) {
    $result = $mysqli->query('SELECT DISTINCT relation FROM relations');
    $relations = [];
    while ($row = $result->fetch_assoc()) {
        $relations[] = $row;
    }
    echo json_encode($relations);
});

$app->get('/users', function () use ($mysqli) {
    $result = $mysqli->query('SELECT username, score FROM users');
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode($users);
});

$app->post('/users/create', function () use ($mysqli) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $query = "INSERT INTO users (username, password, score) VALUES ('$username', '$password', 0)";
    if ($mysqli->query($query) === true) {
        echo json_encode(['message' => 'User created successfully']);
    } else {
        echo json_encode(['message' => 'Error: ' . $mysqli->error]);
    }
});

$app->get('/help', function () {
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
});
