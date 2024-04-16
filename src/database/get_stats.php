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

$sql = "SELECT * FROM your_table"; // replace with query
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo "nbConcepts: " . $row["nbConcepts"] " - nbRelations: " . $row["nbRelations"]." - nbFacts: " . $row["nbFacts"].  - nbUsers: " . $row["nbUsers"]. "br>";
  }
} else {
  echo "0 results";
}
$conn->close();
