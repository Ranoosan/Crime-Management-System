<?php
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

// Handle deletion
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM history_notes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "History note deleted successfully!";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();

// Redirect back to view history
header("Location: view_history.php");
exit();
?>
