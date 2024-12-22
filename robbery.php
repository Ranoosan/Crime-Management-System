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

    // Retrieve the user ID from the session
    $user_id = $_SESSION['user_id'];

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

  // Ensure the user's ID is stored in the session

    // Handling the file upload
    $target_dir = "uploads/";
    $evidence_attachment = $_FILES['evidence_attachment']['name'];
    $target_file = $target_dir . basename($evidence_attachment);

    // Ensure upload folder exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);  // Create folder if it doesn't exist
    }

    // Move uploaded file to target directory
    if (!empty($evidence_attachment) && move_uploaded_file($_FILES["evidence_attachment"]["tmp_name"], $target_file)) {
        echo "The file " . htmlspecialchars(basename($evidence_attachment)) . " has been uploaded.";
    } elseif (empty($evidence_attachment)) {
        $target_file = NULL;  // No file uploaded
    } else {
        echo "Sorry, there was an error uploading your file.";
    }


// Insert the data into the database
$sql = "INSERT INTO robbery_complaints (
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
    $target_file, $suspect_name, $suspect_details, $user_id // Here user_id is being bound
);

    // Execute and check if successful
    if ($stmt->execute()) {
        echo "Robbery complaint submitted successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement
    $stmt->close();
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
        $query = "SELECT * FROM robbery_complaints WHERE id = ? AND user_id = ?";
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
        $query = "SELECT * FROM robbery_complaints WHERE complainant_nic = ? AND user_id = ?";
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
    <title>Robbery Complaint Form</title>
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
        input[type="number"],
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
        }
        .success-message {
            color: green;
        }
    </style>
</head>
<body>

<h2>Your Complaint Status</h2>

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

<?php if (!empty($error_message)): ?>
    <div class="error-message"><?= $error_message; ?></div>
<?php elseif (!empty($complaint)): ?>
    <div class="success-message">
        <h2>Complaint Details</h2>
        <p><strong>Case ID:</strong> <?= $complaint['id']; ?></p>
        <p><strong>Reporter:</strong> <?= $complaint['complainant_full_name']; ?></p>
        <p><strong>Status:</strong> <?= isset($complaint['action']) ? $complaint['action'] : 'Not available'; ?></p>
    </div>
<?php endif; ?>

<div class="content">
    <h1>Submit a Robbery Complaint</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="complainant_title">Title:</label>
        <input type="text" id="complainant_title" name="complainant_title" required>

        <label for="complainant_full_name">Full Name:</label>
        <input type="text" id="complainant_full_name" name="complainant_full_name" required>

        <label for="complainant_address">Address:</label>
        <input type="text" id="complainant_address" name="complainant_address" required>
 <!-- Automatically fill NIC from session -->
 <label for="complainant_nic">NIC Number</label>
            <input type="text" name="complainant_nic" id="complainant_nic" value="<?= $nic ?>" readonly>


        <label for="complainant_district">District:</label>
        <input type="text" id="complainant_district" name="complainant_district" required>

        <label for="complainant_mobile">Mobile Number:</label>
        <input type="text" id="complainant_mobile" name="complainant_mobile" required>

        <label for="complainant_email">Email:</label>
        <input type="email" id="complainant_email" name="complainant_email" required>

        <label for="complainant_age">Age:</label>
        <input type="number" id="complainant_age" name="complainant_age" required>

        <label>Gender:</label>
        <input type="radio" name="complainant_gender" value="male" required> Male
        <input type="radio" name="complainant_gender" value="female" required> Female

        <label for="occurrence_date">Occurrence Date:</label>
        <input type="date" id="occurrence_date" name="occurrence_date" required>

        <label for="place_of_occurrence">Place of Occurrence:</label>
        <input type="text" id="place_of_occurrence" name="place_of_occurrence" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>

        <label for="suspect_name">Suspect's Name (optional):</label>
        <input type="text" id="suspect_name" name="suspect_name">

        <label for="suspect_details">Suspect Details (optional):</label>
        <input type="text" id="suspect_details" name="suspect_details">

        <label for="evidence_attachment">Evidence Attachment (optional):</label>
        <input type="file" id="evidence_attachment" name="evidence_attachment">

        <input type="submit" value="Submit Complaint">
    </form>
</div>

</body>
</html>
