<?php
session_start();



// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crime_management"; // Your database name

// Create a new connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the application ID is provided
if (isset($_GET['id'])) {
    $application_id = $_GET['id'];

    // Sanitize the input to prevent SQL injection
    $application_id = intval($application_id); // Convert to integer

    // SQL query to delete the application
    $sql = "DELETE FROM job_applications_with_cv WHERE id = ?";

    // Prepare statement to avoid SQL injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $application_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo "<script>alert('Application deleted successfully.'); window.location.href = 'admin_view_applications.php';</script>";
    } else {
        echo "<script>alert('Failed to delete the application.'); window.location.href = 'admin_view_applications.php';</script>";
    }

    // Close the prepared statement
    $stmt->close();
} else {
    echo "<script>alert('No application ID provided.'); window.location.href = 'admin_view_applications.php';</script>";
}

// Close the connection
$conn->close();
?>
