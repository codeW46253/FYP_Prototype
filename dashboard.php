<?php
// Include the database configuration file
include 'config.php';

// Start the session to retrieve the user ID from login.php
session_start();

// Check if the user is logged in and the user ID is set
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Update the last_login field with current server time
$sql_update_last_login = "UPDATE users SET last_login = NOW() WHERE id = ?";
$stmt_update = mysqli_prepare($conn, $sql_update_last_login);
mysqli_stmt_bind_param($stmt_update, "i", $user_id);
mysqli_stmt_execute($stmt_update);
mysqli_stmt_close($stmt_update);

// Query to retrieve data for the selected user ID
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the user data
if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
} else {
    echo "No data found for the user.";
    exit();
}

// Pass the data to dashboard.html
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
        nav {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            background-color: #444;
            padding: 10px 0;
            border-bottom: 3px solid #007BFF;
        }
        nav a {
            color: white;
            text-decoration: none;
            margin: 5px 10px;
            font-size: 14px;
            font-weight: bold;
            padding: 8px 12px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        nav a:hover {
            background-color: #007BFF;
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
        .buttons a, .buttons button {
            text-decoration: none;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .buttons a:hover, .buttons button:hover {
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
            nav a {
                font-size: 12px;
                padding: 6px 10px;
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
            nav {
                flex-direction: row;
            }
            nav a {
                margin: 5px 0;
            }
            .buttons {
                flex-direction: column;
            }
            .buttons a, button {
                margin: 5px 0;
            }

            .popup {
                position: fixed;
                top: 0;
                right: -100%; /* Initially off-screen */
                width: 300px;
                height: 100%;
                background-color: #f9f9f9;
                box-shadow: -2px 0 5px rgba(0, 0, 0, 0.3);
                transition: right 0.3s ease-in-out; /* Smooth sliding effect */
                z-index: 1000; /* Ensure it appears above other elements */
            }

            .popup.active {
                right: 0; /* Slide into view */
            }
        }
    </style>
</head>
<body>
    <nav>
        <a href="main.php">Main</a>
        <a href="dictionary.php">Dictionary</a>
        <a href="lesson_and_quiz.php">Lesson & Quiz</a>
        <a href="dashboard.php">Dashboard</a>
    </nav>
    <div class="container">
        <h1>Welcome to the Dashboard</h1>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Last Login Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo htmlspecialchars($user_data['username']); ?></td>
                    <td><?php echo htmlspecialchars($user_data['email']); ?></td>
                    <td><?php echo htmlspecialchars($user_data['last_login']); ?></td>
                </tr>
            </tbody>
        </table>
        <div class="buttons">
            <button id="editProfileButton">Edit Profile</button>
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <footer>
        &copy; University Poly-Tech Malaysia 2025
    </footer>

    <div id="editProfilePopup" class="popup">
        <div class="popup-content">
            <h2>Edit Profile</h2>
            <form action="edit_profile.php" method="post" onsubmit="validatePasswords()">
                <table>
                    <tr>
                        <td><label for="username">Username:</label></td>
                        <td><input type="text" id="username" name="username" required></td>
                    </tr>
                    <tr>
                        <td><label for="email">Email:</label></td>
                        <td><input type="email" id="email" name="email" required></td>
                    </tr>
                    <tr>
                        <td><label for="new_password">New Password:</label></td>
                        <td><input type="password" id="new_password" name="new_password" placeholder="--optional--" minlength="8" maxlength="16"></td>
                    </tr>
                    <tr>
                        <td><label for="confirm_password">Confirm Password:</label></td>
                        <td><input type="password" id="confirm_password" name="confirm_password" placeholder="--optional--" minlength="8" maxlength="16"></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <input type="checkbox" id="show_passwords" onclick="togglePasswordVisibility()"> Show Passwords
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <button type="submit">Save</button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <button id="closePopup" type="button">Close</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>

</body>
    <script>
        function validatePasswords() {
                const newPassword = document.getElementById("new_password").value;
                const confirmPassword = document.getElementById("confirm_password").value;
                
                if (newPassword !== confirmPassword) {
                    alert("New Password and Confirm Password do not match.");
                    return false;
                }
                return true;
            }
            
            function togglePasswordVisibility() {
                const newPassword = document.getElementById("new_password");
                const confirmPassword = document.getElementById("confirm_password");
                const showPasswords = document.getElementById("show_passwords");

                if (showPasswords.checked) {
                    newPassword.type = "text";
                    confirmPassword.type = "text";
                } else {
                    newPassword.type = "password";
                    confirmPassword.type = "password";
                }
            }

        document.addEventListener("DOMContentLoaded", function () {
            const popup = document.getElementById("editProfilePopup");
            const openButton = document.getElementById("editProfileButton");
            const closeButton = document.getElementById("closePopup");
            
            // Open the popup when the button is clicked
            openButton.addEventListener("click", function () {
                console.log("active:added");
                popup.classList.add("active");
            });

            // Close the popup when the close button is clicked
            closeButton.addEventListener("click", function () {
                console.log("active:removed");
                popup.classList.remove("active");
            });
        });
    </script>
</html>