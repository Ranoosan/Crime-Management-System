<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crime_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please log in to submit the form.");
}

// Assigning user inputs to variables
$user_id = $_SESSION['user_id'];
$full_name = $_POST['full_name'];
$dob = $_POST['dob'];
$gender = $_POST['gender'];
$nationality = $_POST['nationality'];
$id_number = $_POST['id_number'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$current_address = $_POST['current_address'];
$previous_address = $_POST['previous_address'] ?: null;
$occupation = $_POST['occupation'] ?: null;
$employer_name = $_POST['employer_name'] ?: null;
$employer_address = $_POST['employer_address'] ?: null;
$employer_contact_number = $_POST['employer_contact_number'] ?: null;
$reason = $_POST['reason'];
$country_applying_for = $_POST['country_applying_for'] ?: null;
$criminal_history = $_POST['criminal_history'] ?: null;

// Handling file uploads
$target_dir = "uploads/";
$id_upload_path = $target_dir . basename($_FILES["id_upload"]["name"]);
$photo_upload_path = $target_dir . basename($_FILES["photo_upload"]["name"]);

// Move uploaded files to the server
if (!move_uploaded_file($_FILES["id_upload"]["tmp_name"], $id_upload_path) || 
    !move_uploaded_file($_FILES["photo_upload"]["tmp_name"], $photo_upload_path)) {
    die("Sorry, there was an error uploading your files.");
}

// Insert data into the database
$sql = "INSERT INTO police_clearance_applications 
        (user_id, full_name, dob, gender, nationality, id_number, email, phone, 
        current_address, previous_address, occupation, employer_name, employer_address, 
        employer_contact_number, reason, country_applying_for, criminal_history, 
        id_upload_path, photo_upload_path) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("issssssssssssssssss",
    $user_id, $full_name, $dob, $gender, $nationality, $id_number, $email, $phone,
    $current_address, $previous_address, $occupation, $employer_name, $employer_address,
    $employer_contact_number, $reason, $country_applying_for, $criminal_history, 
    $id_upload_path, $photo_upload_path);

if ($stmt->execute()) {
    echo "Application submitted successfully!";
} else {
    echo "Error: " . $stmt->error;
}

// Close the database connection
$stmt->close();
$conn->close();
?>
