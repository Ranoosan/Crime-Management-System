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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nic_number = $_POST['nic_number'];
    $full_name = $_POST['full_name'];
    $place = $_POST['place'];
    $case_details = $_POST['case_details'];
    $fine_amount = $_POST['fine_amount'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO traffic_fines (nic_number, full_name, place, case_details, fine_amount) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssd", $nic_number, $full_name, $place, $case_details, $fine_amount);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Fine added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="complaints.css"> <!-- Link to your CSS file -->
    <title>Add Traffic Fine</title>
    <style>
        
    </style>
</head>
<body>
    <h1>Add Traffic Fine</h1>
    <form method="post" action="add_fine_traffic.php">
        <label for="nic_number">NIC Number:</label>
        <input type="text" id="nic_number" name="nic_number" required><br><br>

        <label for="full_name">Full Name:</label>
        <input type="text" id="full_name" name="full_name" required><br><br>

        <label for="place">Place:</label>
        <input type="text" id="place" name="place" required><br><br>

        <label for="case_details">Case Details:</label><br>
        <textarea id="case_details" name="case_details" rows="4" cols="50" required></textarea><br><br>

        <label for="fine_amount">Fine Amount (in LKR):</label>
        <input type="number" id="fine_amount" name="fine_amount" step="0.01" required><br><br>

        <input type="submit" value="Add Fine">
    </form>
</body>
</html>
