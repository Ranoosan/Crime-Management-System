<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data with sanitization
    $parentTitle = htmlspecialchars($_POST['parent_title']);
    $parentFullName = htmlspecialchars($_POST['parent_full_name']);
    $relationToVictim = htmlspecialchars($_POST['relation_to_victim']);
    $parentAddress = htmlspecialchars($_POST['parent_address']);
    $parentNic = htmlspecialchars($_POST['parent_nic']);
    $parentDistrict = htmlspecialchars($_POST['parent_district']);
    $parentMobile = htmlspecialchars($_POST['parent_mobile']);
    $parentAge = intval($_POST['parent_age']);
    $parentGender = htmlspecialchars($_POST['parent_gender']);
    $childTitle = htmlspecialchars($_POST['child_title']);
    $childFullName = htmlspecialchars($_POST['child_full_name']);
    $childAge = intval($_POST['child_age']);
    $childGender = htmlspecialchars($_POST['child_gender']);
    $incidentDescription = htmlspecialchars($_POST['incident_description']);

    // Handle file uploads
    $nicFront = $_FILES['nic_front'];
    $nicBack = $_FILES['nic_back'];

    // Validate uploads
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxFileSize = 2 * 1024 * 1024; // 2 MB

    $uploadDir = 'uploads/';

    // Check file errors and validate file types and sizes
    if ($nicFront['error'] == 0 && $nicBack['error'] == 0) {
        if (in_array($nicFront['type'], $allowedMimeTypes) && in_array($nicBack['type'], $allowedMimeTypes) &&
            $nicFront['size'] <= $maxFileSize && $nicBack['size'] <= $maxFileSize) {

            // Move files to the upload directory
            move_uploaded_file($nicFront['tmp_name'], $uploadDir . basename($nicFront['name']));
            move_uploaded_file($nicBack['tmp_name'], $uploadDir . basename($nicBack['name']));

            // Database connection
            $servername = "localhost"; // Update as necessary
            $username = "root"; // Update as necessary
            $password = ""; // Update as necessary
            $dbname = "crime_management"; // Update as necessary

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Prepare an SQL statement to prevent SQL injection
            $stmt = $conn->prepare("INSERT INTO abuse_complaint (parent_title, parent_full_name, relation_to_victim, parent_address, parent_nic, parent_district, parent_mobile, parent_age, parent_gender, child_title, child_full_name, child_age, child_gender, incident_description, nic_front, nic_back) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt === false) {
                die("Error preparing statement: " . $conn->error);
            }

            // Bind parameters
            $stmt->bind_param("ssssssiissssisss", $parentTitle, $parentFullName, $relationToVictim, $parentAddress, $parentNic, $parentDistrict, $parentMobile, $parentAge, $parentGender, $childTitle, $childFullName, $childAge, $childGender, $incidentDescription, $nicFront['name'], $nicBack['name']);

            // Execute the statement
            if ($stmt->execute()) {
                echo "Complaint submitted successfully!";
            } else {
                echo "Error: " . $stmt->error;
            }

            // Close connections
            $stmt->close();
            $conn->close();
        } else {
            echo "File type or size is not valid.";
        }
    } else {
        echo "Error uploading files.";
    }
}
?>
