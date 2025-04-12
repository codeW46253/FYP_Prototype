<?php
// Include the database configuration file
include 'config.php';

// Start session to get user ID
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the user ID (assuming it's stored in the session)
    $userId = $_SESSION['user_id'];

    // Get form inputs
    $newUsername = mysqli_real_escape_string($conn, $_POST['username']);
    $newEmail = mysqli_real_escape_string($conn, $_POST['email']);
    $newPassword = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Update username and email
    $updateQuery = "UPDATE users SET username = '$newUsername', email = '$newEmail' WHERE id = $userId";
    if (mysqli_query($conn, $updateQuery)) {
        // Check if password fields are not empty
        if (!empty($newPassword) && !empty($confirmPassword)) {
            // Check if new password and confirm password match
            if ($newPassword === $confirmPassword) {
                // Hash the new password
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

                // Update the password in the database
                $passwordQuery = "UPDATE users SET password = '$hashedPassword' WHERE id = $userId";
                if (!mysqli_query($conn, $passwordQuery)) {
                    echo "Error updating password: " . mysqli_error($conn);
                }
            } else {
                header("Location: dashboard.php");
                exit;
            }
        }
        // Redirect to dashboard
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Error updating profile: " . mysqli_error($conn);
    }
}
?>