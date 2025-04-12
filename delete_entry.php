<?php
session_start();
require_once "config.php";

// Ensure the request method is POST for security
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dictionary_id = intval($_POST['id']); // Sanitize the ID
    $logged_in_user_id = $_SESSION['id'];  // Logged-in user's ID

    // Verify the user owns the entry
    $sql = "SELECT user_id FROM Dictionary WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $dictionary_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $owner_id);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($logged_in_user_id !== $owner_id) {
        die("Unauthorized access.");
    }

    // Delete the dictionary entry
    $sql_delete = "DELETE FROM Dictionary WHERE id = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);
    mysqli_stmt_bind_param($stmt_delete, "i", $dictionary_id);

    if (mysqli_stmt_execute($stmt_delete)) {
        echo json_encode(["status" => "success", "message" => "Entry deleted successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error deleting entry."]);
    }

    mysqli_stmt_close($stmt_delete);

    $sql_access_delete = "DELETE FROM DictionaryAccess WHERE dictionary_id = ?";
    $stmt_access_delete = mysqli_prepare($conn, $sql_access_delete);
    mysqli_stmt_bind_param($stmt_access_delete, "i", $dictionary_id);
    mysqli_stmt_execute($stmt_access_delete);
    mysqli_stmt_close($stmt_access_delete);
}
?>