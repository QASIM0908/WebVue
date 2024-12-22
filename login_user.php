<?php
include 'connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) { // Check if prepare failed
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("s", $email);

    if ($stmt->execute() === false) { // Check if execute failed
        die("Error executing statement: " . $stmt->error);
    }

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            echo "Login successful! Redirecting to <a href='home.php'>home</a> page."; // This message will likely not be displayed due to the redirect
            header("Location: home.php");
            exit; // Important: Stop script execution after redirect
        } else {
            echo "Invalid password!";
            exit; // Important: Stop script execution
        }
    } else {
        echo "Email not registered!";
        exit; // Important: Stop script execution
    }

     $stmt->close();
}

$conn->close(); // Close the connection at the very end of the script
?>