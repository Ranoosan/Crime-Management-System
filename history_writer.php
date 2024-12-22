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

// Redirect if user is not logged in (implement your own session logic)
// session_start();
// if (!isset($_SESSION['admin_logged_in'])) {
//     header("Location: adminS2login.php");
//     exit();
// }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Writer</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="content">
        <h2>History Notes Management</h2>
        <button onclick="location.href='add_history.php'">Add History Note</button>
        <button onclick="location.href='view_history.php'">View History Notes</button>
        <!-- <button onclick="location.href='edit_history.php'">Edit History Note</button> -->
    </div>
    
    <?php include 'footer.php'; // Include footer ?>
</body>
</html>
