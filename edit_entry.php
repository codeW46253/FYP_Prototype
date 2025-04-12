<?php

require_once('config.php');

if (isset($_GET['id'])) {
  $id = $_GET['id];

  // Fetch the entry details from the database
  $sql = "SELECT * FROM dictionary WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result=$stmt->get_result();
  $entry = $result->fetch_assoc():

  if (!$entry) {
    echo "Entry not found";
    exit:
  }
}

?>
