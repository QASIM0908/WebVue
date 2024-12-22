<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "techease";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4"); // Set character set

// You can add a success message for testing:
// echo "Connected successfully";

// ... your database interaction code here ...

// $conn->close(); // Close the connection when done (usually at the end of the script)
?>