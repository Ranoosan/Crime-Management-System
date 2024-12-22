<?php
// Start session
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'crime_management');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$complaint = null;
$error_message = '';
$success_message = '';
$user_nic = ''; // Initialize the NIC variable

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You need to be logged in to view your complaints.");
}

// Logged-in user ID
$logged_in_user_id = $_SESSION['user_id'];

// Check if either 'id' or 'nic' is provided via GET method
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Filter by Complaint ID and logged-in user's ID
    $id = intval($_GET['id']);
    
    // Fetch complaint details from the database using the id and user ID
    $sql = "SELECT action_level, id, reporter_full_name, victim_full_name, incident_description 
            FROM child_abuse_complaints 
            WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $logged_in_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $complaint = $result->fetch_assoc();
    
    if (!$complaint) {
        $error_message = "Complaint not found for ID: $id";
    }
} elseif (isset($_GET['nic']) && !empty($_GET['nic'])) {
    // Filter by NIC Number and logged-in user's ID
    $nic = $_GET['nic'];
    
    // Fetch complaint details from the database using the NIC and user ID
    $sql = "SELECT action_level, id, reporter_full_name, victim_full_name, incident_description 
            FROM child_abuse_complaints 
            WHERE reporter_nic = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nic, $logged_in_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $complaint = $result->fetch_assoc();
    
    if (!$complaint) {
        $error_message = "Complaint not found for NIC: $nic";
    }
}
// Fetch the logged-in user's NIC from the database
$sql = "SELECT nic_number FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $logged_in_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        $user_nic = $user_data['nic_number']; // Get the NIC of the logged-in user
    }
} else {
    $error_message = "Error fetching user NIC: " . $conn->error;
}


// Handling the form submission for a new complaint
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $reporter_title = $_POST['reporter_title'];
    $reporter_full_name = $_POST['reporter_full_name'];
    $reporter_address = $_POST['reporter_address'];
    $reporter_nic = $_POST['reporter_nic'];
    $reporter_district = $_POST['reporter_district'];
    $reporter_mobile = $_POST['reporter_mobile'];
    $reporter_email = $_POST['reporter_email'];
    $reporter_age = $_POST['reporter_age'];
    $reporter_gender = $_POST['reporter_gender'];
    $victim_full_name = $_POST['victim_full_name'];
    $victim_age = $_POST['victim_age'];
    $victim_gender = $_POST['victim_gender'];
    $incident_date = $_POST['incident_date'];
    $incident_place = $_POST['incident_place'];
    $incident_description = $_POST['incident_description'];
    
    // Handle file upload
    $evidence_attachment = $_FILES['evidence_attachment']['name'];
    $upload_dir = 'uploads/';
    $file_path = $upload_dir . basename($evidence_attachment);
    
    if (move_uploaded_file($_FILES['evidence_attachment']['tmp_name'], $file_path)) {
        // Insert into database, including user ID in the insert query
        $sql = "INSERT INTO child_abuse_complaints (reporter_title, reporter_full_name, reporter_address, reporter_nic, reporter_district, 
                reporter_mobile, reporter_email, reporter_age, reporter_gender, victim_full_name, victim_age, victim_gender, 
                incident_date, incident_place, incident_description, evidence_attachment, user_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssssssssi", $reporter_title, $reporter_full_name, $reporter_address, $reporter_nic, $reporter_district,
                        $reporter_mobile, $reporter_email, $reporter_age, $reporter_gender, $victim_full_name, $victim_age, $victim_gender, 
                        $incident_date, $incident_place, $incident_description, $file_path, $logged_in_user_id);
        
        if ($stmt->execute()) {
            $success_message = "Complaint submitted successfully!";
        } else {
            $error_message = "Error submitting complaint: " . $stmt->error;
        }
    } else {
        $error_message = "Error uploading evidence attachment.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child Abuse Complaint Status and Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            text-align: center;
        }
        .alert {
            padding: 15px;
            background-color: #f44336;
            color: white;
            margin-bottom: 20px;
        }
        .success {
            padding: 15px;
            background-color: #4CAF50;
            color: white;
            margin-bottom: 20px;
        }
        .content {
            margin: 20px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"], input[type="email"], input[type="file"], input[type="number"], textarea, select {
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
        <label for="reporter_nic">NIC Number:</label>
        <input type="text" id="reporter_nic" name="nic" placeholder="Enter NIC Number">
        <input type="submit" value="View Complaint">
    </form>

    <!-- Display Error or Complaint Details -->
    <?php if (!empty($error_message)): ?>
        <div class="alert"><?= $error_message; ?></div>
    <?php elseif ($complaint): ?>
        <div class="success">
            <h2>Complaint Details</h2>
            <p><strong>Case ID:</strong> <?= $complaint['id']; ?></p>
            <p><strong>Reporter:</strong> <?= $complaint['reporter_full_name']; ?></p>
            <p><strong>Victim:</strong> <?= $complaint['victim_full_name']; ?></p>
            <p><strong>Incident Description:</strong> <?= $complaint['incident_description']; ?></p>
            <p><strong>Action Level:</strong> <?= $complaint['action_level']; ?></p>
        </div>
    <?php endif; ?>

    <!-- Complaint Form Section -->
    <h2>Submit New Complaint</h2>
    <?php if (!empty($success_message)): ?>
        <div class="success"><?= $success_message; ?></div>
    <?php endif; ?>
    <form method="POST" action="" enctype="multipart/form-data">
        <!-- Reporter Information -->
        <h3>Your Information</h3>
        <label for="reporter-title">Title (Mr/Mrs/Ms):</label>
        <input type="text" name="reporter_title" id="reporter-title" required>

        <label for="reporter-fullname">Full Name:</label>
        <input type="text" name="reporter_full_name" id="reporter-fullname" required>

        <label for="reporter-address">Address:</label>
        <input type="text" name="reporter_address" id="reporter-address" required>

        <label for="reporter-nic">NIC Number:</label>
        <!-- Populate NIC value from the database -->
        <input type="text" name="reporter_nic" id="reporter-nic" value="<?= htmlspecialchars($user_nic); ?>" readonly>
        <label for="reporter-district">District:</label>
        <input type="text" name="reporter_district" id="reporter-district" required>

        <label for="reporter-mobile">Mobile Number:</label>
        <input type="text" name="reporter_mobile" id="reporter-mobile" required>

        <label for="reporter-email">Email Address:</label>
        <input type="email" name="reporter_email" id="reporter-email" required>

        <label for="reporter-age">Age:</label>
        <input type="number" name="reporter_age" id="reporter-age" required>

        <label for="reporter-gender">Gender:</label>
        <select name="reporter_gender" id="reporter-gender" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <!-- Victim Information -->
        <h3>Victim Information</h3>
        <label for="victim-fullname">Victim's Full Name:</label>
        <input type="text" name="victim_full_name" id="victim-fullname" required>

        <label for="victim-age">Victim's Age:</label>
        <input type="number" name="victim_age" id="victim-age" required>

        <label for="victim-gender">Victim's Gender:</label>
        <select name="victim_gender" id="victim-gender" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <!-- Incident Information -->
        <h3>Incident Information</h3>
        <label for="incident-date">Incident Date:</label>
        <input type="date" name="incident_date" id="incident-date" required>

        <label for="incident-place">Incident Place:</label>
        <input type="text" name="incident_place" id="incident-place" required>

        <label for="incident-description">Incident Description:</label>
        <textarea name="incident_description" id="incident-description" rows="5" required></textarea>

        <label for="evidence-attachment">Evidence Attachment (if any):</label>
        <input type="file" name="evidence_attachment" id="evidence-attachment">

        <input type="submit" value="Submit Complaint">
    </form>
</div>

</body>
</html> 