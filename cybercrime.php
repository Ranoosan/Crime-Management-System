<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crime_management"; // Ensure this database and table are created

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view or submit complaints.");
}

// Fetch the NIC associated with the logged-in user
$nic = "";
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // Fetch user ID from session
    $query = "SELECT nic_number FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id); // Bind the user_id
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        $nic = $user_data['nic_number']; // Store NIC in variable
    } else {
        $nic = ""; // NIC not found, leave empty
    }
}

// Handling the form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {

 
    // Collecting form data
    $complainant_title = $_POST['complainant_title'];
    $complainant_full_name = $_POST['complainant_full_name'];
    $complainant_address = $_POST['complainant_address'];
    $complainant_nic = $_POST['complainant_nic'];
    $complainant_district = $_POST['complainant_district'];
    $complainant_mobile = $_POST['complainant_mobile'];
    $complainant_email = $_POST['complainant_email'];
    $complainant_age = $_POST['complainant_age'];
    $complainant_gender = $_POST['complainant_gender'];
    $occurrence_date = $_POST['occurrence_date'];
    $place_of_occurrence = $_POST['place_of_occurrence'];
    $description = $_POST['description'];
    $suspect_name = $_POST['suspect_name'] ?? '';
    $suspect_details = $_POST['suspect_details'] ?? '';

    // Handling the file upload
    $target_dir = "uploads/";
    $evidence_attachment = $_FILES['evidence_attachment']['name'];
    $target_file = $target_dir . basename($evidence_attachment);

    // Move uploaded file to target directory
    if ($_FILES['evidence_attachment']['error'] == 0) {
        if (!move_uploaded_file($_FILES["evidence_attachment"]["tmp_name"], $target_file)) {
            die("Sorry, there was an error uploading your file.");
        }
    } else {
        die("Error uploading file: " . $_FILES['evidence_attachment']['error']);
    }

    // Insert the data into the database
    $sql = "INSERT INTO cyber_crime_complaints (
                complainant_title, complainant_full_name, complainant_address, complainant_nic, 
                complainant_district, complainant_mobile, complainant_email, complainant_age, 
                complainant_gender, occurrence_date, place_of_occurrence, description, 
                evidence_attachment, suspect_name, suspect_details, user_id
            ) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Preparing statement to prevent SQL injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssssssssssss",
        $complainant_title, $complainant_full_name, $complainant_address, $complainant_nic, 
        $complainant_district, $complainant_mobile, $complainant_email, $complainant_age, 
        $complainant_gender, $occurrence_date, $place_of_occurrence, $description, 
        $target_file, $suspect_name, $suspect_details, $user_id // Use user ID from session
    );

    // Execute and check if successful
    if ($stmt->execute()) {
        $success_message = "Complaint submitted successfully.";
    } else {
        $error_message = "Error: " . $stmt->error;
    }

    // Close connections
    $stmt->close();
    $conn->close();
}

// Handling GET request for searching complaint based on Case ID or NIC
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $complaint = null; // Initialize complaint variable
    $error_message = ""; // Variable to hold error messages

    // Retrieve logged-in user ID
    $user_id = $_SESSION['user_id'];

    // Check for Case ID
    if (!empty($_GET['id'])) { // If Case ID is provided
        $case_id = $_GET['id'];

        // Query to fetch complaint based on Case ID and user ID
        $query = "SELECT * FROM cyber_crime_complaints WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $case_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $complaint = $result->fetch_assoc(); // Fetch the complaint data
        } else {
            $error_message = "No complaint found for Case ID: " . htmlspecialchars($case_id);
        }
    }
    
    // Check for NIC
    elseif (!empty($_GET['nic'])) { // If NIC is provided
        $nic = $_GET['nic'];

        // Query to fetch complaint based on NIC and user ID
        $query = "SELECT * FROM cyber_crime_complaints WHERE complainant_nic = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $nic, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $complaint = $result->fetch_assoc(); // Fetch the complaint data
        } else {
            $error_message = "No complaint found for NIC: " . htmlspecialchars($nic);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyber Crime Complaint Form</title>
    <style>
        .content {
            margin: 20px;
        }

        h3 {
            margin-top: 20px;
            font-size: 1.2em;
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="file"],
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .error-message {
            color: red;
            font-weight: bold;
        }

        .success-message {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Child Abuse Complaint Status</h1>

    <!-- Complaint Status Section -->
    <h2>Check Complaint Status</h2>
    <form action="" method="GET">
        <label for="case_id">Case ID:</label>
        <input type="text" id="case_id" name="id" placeholder="Enter Case ID">
        <input type="submit" value="View Complaint">
    </form>
    
    <form action="" method="GET">
        <label for="complainant_nic">NIC Number:</label>
        <input type="text" id="complainant_nic" name="nic" placeholder="Enter NIC Number">
        <input type="submit" value="View Complaint">
    </form>

    <!-- Display Error or Complaint Details -->
    <?php if (!empty($error_message)): ?>
        <div class="error-message"><?= $error_message; ?></div>
    <?php elseif (!empty($complaint)): ?>
        <div class="success-message">
            <h2>Complaint Details</h2>
            <p><strong>Case ID:</strong> <?= $complaint['id']; ?></p>
            <p><strong>Reporter:</strong> <?= $complaint['complainant_full_name']; ?></p>
            <p><strong>Status:</strong> <?= isset($complaint['status']) ? $complaint['status'] : 'Not available'; ?></p>
        </div>
    <?php endif; ?>

    <div class="content">
        <h1>Cyber Crime Complaint Form</h1>
        <form method="POST" action="" enctype="multipart/form-data">
            <!-- Personal Information -->
            <h3>Your Information</h3>
            <label for="complainant-title">Title (Mr/Mrs/Ms):</label>
            <select id="complainant-title" name="complainant_title">
                <option value="Mr">Mr</option>
                <option value="Mrs">Mrs</option>
                <option value="Ms">Ms</option>
            </select>

            <label for="complainant-full-name">Full Name:</label>
            <input type="text" id="complainant-full-name" name="complainant_full_name">

            <label for="complainant-address">Address:</label>
            <input type="text" id="complainant-address" name="complainant_address">
           
        <label for="complainant_nic">NIC Number</label>
        <input type="text" name="complainant_nic" id="complainant_nic" value="<?= $nic ?>" readonly>

            <label for="complainant-district">District:</label>
            <input type="text" id="complainant-district" name="complainant_district">

            <label for="complainant-mobile">Mobile:</label>
            <input type="text" id="complainant-mobile" name="complainant_mobile">

            <label for="complainant-email">Email:</label>
            <input type="email" id="complainant-email" name="complainant_email">

            <label for="complainant-age">Age:</label>
            <input type="text" id="complainant-age" name="complainant_age">

            <label for="complainant-gender">Gender:</label>
            <input type="text" id="complainant-gender" name="complainant_gender">

            <!-- Incident Information -->
            <h3>Incident Information</h3>
            <label for="occurrence-date">Date of Occurrence:</label>
            <input type="date" id="occurrence-date" name="occurrence_date">

            <label for="place-of-occurrence">Place of Occurrence:</label>
            <input type="text" id="place-of-occurrence" name="place_of_occurrence">

            <label for="description">Description of Incident:</label>
            <textarea id="description" name="description" rows="5"></textarea>

            <!-- Suspect Information (Optional) -->
            <h3>Suspect Information (Optional)</h3>
            <label for="suspect-name">Suspect Name:</label>
            <input type="text" id="suspect-name" name="suspect_name">

            <label for="suspect-details">Suspect Details:</label>
            <textarea id="suspect-details" name="suspect_details" rows="5"></textarea>

            <!-- Evidence Attachment -->
            <h3>Attach Evidence (if any)</h3>
            <input type="file" name="evidence_attachment" id="evidence_attachment">

            <!-- Submit Button -->
            <input type="submit" value="Submit Complaint">
        </form>
    </div>
</body>
</html>
