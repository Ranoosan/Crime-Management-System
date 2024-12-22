<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crime_management"; // Ensure this database exists

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session to access user_id
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to submit a report.");
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Handling the form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collecting form data
    $complainant_full_name = $_POST['complainant_full_name'];
    $complainant_address = $_POST['complainant_address'];
    $complainant_nic = $_POST['complainant_nic'];
    $complainant_mobile = $_POST['complainant_mobile'];
    $complainant_email = $_POST['complainant_email'];
    $missing_full_name = $_POST['missing_full_name'];
    $missing_age = $_POST['missing_age'];
    $missing_gender = $_POST['missing_gender'];
    $missing_description = $_POST['missing_description'];
    $last_seen_date = $_POST['last_seen_date'];
    $last_seen_location = $_POST['last_seen_location'];
    $incident_description = $_POST['incident_description'];

    // Handling file upload
    $target_dir = "uploads/";
    $evidence_attachment = $_FILES['evidence_attachment']['name'];
    $target_file = $target_dir . basename($evidence_attachment);

    // Move uploaded file to target directory
    if (move_uploaded_file($_FILES["evidence_attachment"]["tmp_name"], $target_file)) {
        echo "The file " . htmlspecialchars(basename($evidence_attachment)) . " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }

// Insert the data into the database
$sql = "INSERT INTO missing_person_reports (
    complainant_full_name, complainant_address, complainant_nic,
    complainant_mobile, complainant_email, missing_full_name,
    missing_age, missing_gender, missing_description, 
    last_seen_date, last_seen_location, incident_description,
    evidence_attachment, user_id
) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

// Preparing statement to prevent SQL injection
$stmt = $conn->prepare($sql);
$stmt->bind_param(
"ssssssssssssis", // Change the last 's' to 'i' if user_id is an integer
$complainant_full_name, $complainant_address, $complainant_nic,
$complainant_mobile, $complainant_email, $missing_full_name,
$missing_age, $missing_gender, $missing_description,
$last_seen_date, $last_seen_location, $incident_description,
$target_file,
$user_id // Include user_id here
);

// Execute and check if successful
if ($stmt->execute()) {
echo "Report submitted successfully.";
} else {
echo "Error: " . $stmt->error;
}

// Close connections
$stmt->close();
$conn->close();

}
?>
