<?php
require_once 'config.php'; // Include the database connection from config.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $email    = isset($_POST['email'])    ? trim($_POST['email'])    : '';

    if (!empty($username) && !empty($password)) {
        // Check if the username already exists
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            header("Location: signup.html?error=AccountAlreadyExistErr");
            exit();
        } else {
            // Insert the new user into the database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username, $hashedPassword, $email);

            if ($stmt->execute()) {
                echo "Sign-up successful. Redirecting to the sign-in page...";
                header("Refresh: 2; URL=login.html"); // Redirect to the sign-in page after 2 seconds
                exit();
            } else {
                echo "Error: Could not complete the sign-up process.";
            }
        }

        $stmt->close();
    } else {
        echo "Please fill in all fields.";
    }
} else {
    // Redirect to the sign-up page if the request method is not POST
    header("Location: signup.html");
    exit();
}

$conn->close();
?>
