<?php
session_start();

// Only allow access if the admin is logged in
if (!isset($_SESSION['admin_email'])) {
    header("Location: adminS2login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crime_management";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all law notes
$sql = "SELECT * FROM law_notes ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Law Notes</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS -->
</head>
<body>
    <div class="content">
        <h2>Edit Law Notes</h2>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div class='note'>";
                echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                echo "<p>" . nl2br(htmlspecialchars($row['note'])) . "</p>";
                echo "<a href='edit_note.php?id=" . $row['id'] . "'>Edit</a>"; // Link to edit the note
                echo "<a href='delete_law.php?id=" . $row['id'] . "'>Delete</a>"; // Link to edit the note
                echo "</div><hr>";
            }
        } else {
            echo "<p>No notes found.</p>";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
