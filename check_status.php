<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crime_management";  // Replace with your actual database name

// Create the connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch complaint ID from the request and sanitize it
$complaint_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($complaint_id > 0) {
    // Prepare the SQL statement to prevent SQL injection
    $query = $conn->prepare("SELECT status FROM complaints WHERE id = ?");
    $query->bind_param("i", $complaint_id);
    $query->execute();
    $result = $query->get_result();

    // Check if a result was returned
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Return the status as a JSON response
        echo json_encode(['status' => $row['status']]);
    } else {
        // If no complaint is found, return an error response
        echo json_encode(['error' => 'Complaint not found']);
    }

    // Close the prepared statement
    $query->close();
} else {
    // If the ID is not valid or not provided, return an error response
    echo json_encode(['error' => 'Invalid complaint ID']);
}

// Close the database connection
$conn->close();
?>
