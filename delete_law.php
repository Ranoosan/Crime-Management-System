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

// Check if ID is set in the URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Get ID from URL

    // Fetch the note with the specified ID to confirm deletion
    $sql = "SELECT * FROM law_notes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the note exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $title = $row['title'];
    } else {
        echo "Note not found.";
        exit();
    }
} else {
    echo "No note ID provided.";
    exit();
}

// Handle deletion confirmation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Delete the note from the database
    $delete_sql = "DELETE FROM law_notes WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $id);

    if ($delete_stmt->execute()) {
        echo "Note deleted successfully!";
        header("Location: view_laws.php"); // Redirect to the view page
        exit();
    } else {
        echo "Error deleting note: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Law Note</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="content">
        <h2>Delete Law Note</h2>
        <p>Are you sure you want to delete the following law note?</p>
        <p><strong>Title:</strong> <?php echo htmlspecialchars($title); ?></p>

        <form action="" method="POST">
            <input type="submit" value="Delete Note">
            <a href="view_laws.php">Cancel</a> <!-- Link to cancel and return -->
        </form>
    </div>
</body>
</html>
