<?php
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "your_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10; // number of records per page
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM faits LIMIT $offset, $limit"; // replace with query
$result = $conn->query($sql);

$data = [];
$data['hasNextPage'] = $result->num_rows == $limit;

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    $data['faits'][] = $row;
  }
} else {
  echo "0 results";
}

$conn->close();

// echo the result in JSON format
echo json_encode($data);
