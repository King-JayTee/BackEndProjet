if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if ($username == "ift3225" && $password == "5223tfi") {
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $username;
        header("location: index.php");
    } else {
        echo "Invalid username or password.";
    }
}