<?php

require_once "config.php";

$id = $_GET['id']; // Get the dictionary ID from the query string
$sql = "SELECT title, content FROM Dictionary WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $title, $content);
mysqli_stmt_fetch($stmt);
echo json_encode(["title" => $title, "content" => $content]);

mysqli_stmt_close($stmt);

?>