<?php

session_start();
require_once "config.php";

// Get logged-in user ID
$logged_in_user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'add_entry') {
        // Retrieve the form data
        $title = htmlspecialchars(trim($_POST['title']));
        $content = htmlspecialchars(trim($_POST['content']));
    
        // Insert the new word into the database
        $sql = "INSERT INTO Dictionary (user_id, title, content) 
                VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iss", $logged_in_user_id, $title, $content);
    
        if (mysqli_stmt_execute($stmt)) {
            echo "<p>Word added successfully!</p>";
        } else {
            echo "<p>Error: Could not add the word.</p>";
        }
    
        mysqli_stmt_close($stmt);
    } elseif ($_POST['action'] === 'edit_entry') {
        
    }
}

// Fetch words created by or viewable to the user
$sql = "SELECT d.id, d.title, d.content, a.access_level 
        FROM Dictionary d
        LEFT JOIN Dictionary_Access a ON d.id = a.dictionary_id AND a.user_id = ?
        WHERE d.user_id = ? OR a.access_level IN ('view-only', 'edit')";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $logged_in_user_id, $logged_in_user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $dictionary_id, $title, $content, $access_level);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dictionary</title>
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
        .buttons button, td a {
            text-decoration: none;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .buttons button:hover, td a:hover {
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
            .buttons button, td a {
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
            .buttons button, td a {
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
        <h1>Dictionary</h1>

        <div class="buttons">
            <button id="addWordButton">Add</button>
        </div>

        <?php
        echo "<table>";
        echo "<thead>
                <tr>
                    <th>Word</th>
                    <th>Meaning</th>
                </tr>
              </thead>";
        echo "<tbody>";
        
        while (mysqli_stmt_fetch($stmt)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($title) . "</td>";
            echo "<td>";
            echo "<a href='word.php?id=" . $dictionary_id . "'>Display</a> ";
            if ($logged_in_user_id == $logged_in_user_id) { // Allow deletion only for the owner
                echo "<a href='delete_entry.php?id=" . $dictionary_id . "' class='deleteButton'>Delete</a>";
            }
            echo "</td>";
            echo "</tr>";
        }
        
        echo "</tbody>";
        echo "</table>";
        ?>
    </div>

    <footer>
        &copy; University Poly-Tech Malaysia 2025
    </footer>

    <div id="addWordPopup" class="popup">
        <div class="popup-content">
            <h2>Add Word</h2>
            <form id="addEntryForm" method="post">
                <input type="hidden" name="action" value="add_entry">
                <table>
                    <tr>
                        <td><label for="title">Word:</label></td>
                        <td><input type="title" id="title" name="title" required></td>
                    </tr>
                    <tr>
                        <td><label for="content">Meaning:</label></td>
                        <td><input type="content" id="content" name="content" required></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <button type="submit">Add</button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <button id="closeAddWordPopup" type="button">Close</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>

    <div id="editWordPopup" class="popup">
        <div class="popup-content">
            <h2>Edit Word</h2>
            <form action="edit_entry.php" method="post">
                <input type="hidden" name="action" value="edit_entry">
                <table>
                    <tr>
                        <td><label for="id">ID:</label></td>
                        <td><input type="hidden" name="id" value="<?php echo htmlspecialchars($entry['id']); ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="title">Word:</label></td>
                        <td><input type="text" name="title" id="title" value="<?php echo htmlspecialchars($entry['title'])?>" required></td>
                    </tr>
                    <tr>
                        <td><label for="translate">Translation:</label></td>
                        <td><input type="text" name="translate" id="translate" value="<?php echo htmlspecialchars($entry['translate'])?>" required></td>
                    </tr>
                    <tr>
                        <td><label for="content">Meaning:</label></td>
                        <td>
                            <textarea name="content" id="content" required><?php echo htmlspecialchars($entry['translate'])?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <button type="submit">Save</button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <button id="closeWordPopup" type="button">Close</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</body>
    <script>
        document.querySelectorAll(".deleteButton").forEach(button => {
            button.addEventListener("click", function (event) {
                event.preventDefault();

                const dictionaryId = button.getAttribute("href").split("=")[1]; // Extract the ID from the link
                if (confirm("Are you sure you want to delete this entry?")) {
                    fetch("delete_entry.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: `id=${dictionaryId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "success") {
                            alert(data.message);
                            location.reload(); // Reload the page to update the dictionary list
                        } else {
                            alert(data.message);
                        }
                    });
                }
            });
        });

        document.getElementById("addEntryForm").addEventListener("submit", function (event) {
            event.preventDefault(); // Prevent the default form submission

            const formData = new FormData(this); // Collect the form data

            fetch("dictionary.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                // Optional: Show a success message
                console.log(data);

                // Reload the page or update the table dynamically
                location.reload(); // Simple reload to reflect changes
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            const popup = document.getElementById("addWordPopup");
            const openButton = document.getElementById("addWordButton");
            const closeButton = document.getElementById("closeAddWordPopup");
            
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
