<?php
session_start();
require_once "config.php";

// Get the dictionary ID from the query string
if (isset($_GET['id'])) {
    $dictionary_id = intval($_GET['id']); // Ensure the ID is an integer

    // Fetch the dictionary entry
    $sql = "SELECT title, content, user_id, translate FROM Dictionary WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $dictionary_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $title, $content, $owner_id, $translate);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Check access permissions
    $logged_in_user_id = $_SESSION['user_id'];

    // Fetch access level if not the owner
    if ($logged_in_user_id !== $owner_id) {
        $sql_access = "SELECT access_level FROM DictionaryAccess WHERE dictionary_id = ? AND user_id = ?";
        $stmt_access = mysqli_prepare($conn, $sql_access);
        mysqli_stmt_bind_param($stmt_access, "ii", $dictionary_id, $logged_in_user_id);
        mysqli_stmt_execute($stmt_access);
        mysqli_stmt_bind_result($stmt_access, $access_level);
        mysqli_stmt_fetch($stmt_access);
        mysqli_stmt_close($stmt_access);

        if (!$access_level || $access_level === 'hidden') {
            die("You do not have access to this entry.");
        }
    }
} else {
    die("Invalid entry.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('fuji.png') no-repeat center center fixed;
            background-size: cover;
            color: #333;
        }
        .container {
            margin: 20px auto;
            width: 90%;
            max-width: 1200px;
            background-color: rgba(255, 255, 255, 0.85);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }
        h1 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 20px;
            font-size: 24px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #f9f9f9;
            border-radius: 5px;
            overflow: hidden;
        }
        table thead {
            background-color: #007BFF;
            color: white;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
            font-size: 14px;
        }
        .buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        .buttons a {
            text-decoration: none;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .buttons a:hover {
            background-color: #0056b3;
        }
        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #555;
        }
        @media (max-width: 768px) {
            h1 {
                font-size: 20px;
            }
            th, td {
                font-size: 12px;
                padding: 8px;
            }
            .buttons a, .buttons button {
                font-size: 12px;
                padding: 8px 15px;
            }
        }
        @media (max-width: 480px) {
            .buttons {
                flex-direction: column;
            }
            .buttons a, button {
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($title); ?></h1>
        <table>
            <thead>
                <tr>
                    <td>Word:</td>
                    <td><?php echo htmlspecialchars($title); ?></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Translation:</td>
                    <td><?php echo nl2br(htmlspecialchars($translate)); ?></td>
                </tr>
                <tr>
                    <td>Meaning:</td>
                    <td><?php echo nl2br(htmlspecialchars($content)); ?></td>
                </tr>
            </tbody>
        </table>
        <div class="buttons">
            <a href="dictionary.php">Back to Dictionary</a>
        </div>
    </div>

    <footer>
        &copy; University Poly-Tech Malaysia 2025
    </footer>
</body>
</html>