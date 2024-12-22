<?php
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'crime_management');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please log in to submit a complaint.");
}

$user_id = $_SESSION['user_id']; // Fetch user ID from session

$complaint = null; // Initialize complaint variable
$error_message = ""; // Variable to hold error messages


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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!empty($_GET['id'])) { // If Case ID is provided
        $case_id = $_GET['id'];
        
        // Query to fetch complaint based on Case ID and user ID
        $query = "SELECT * FROM sexual_abuse_reports WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $case_id, $user_id); // Bind case ID and user ID
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $complaint = $result->fetch_assoc(); // Fetch the complaint data
        } else {
            $error_message = "No complaint found for Case ID: " . htmlspecialchars($case_id) . " under your account.";
        }
    }
    elseif (!empty($_GET['nic'])) { // If NIC is provided
        $nic = $_GET['nic'];
        
        // Query to fetch complaint based on NIC and user ID
        $query = "SELECT * FROM sexual_abuse_reports WHERE complainant_nic = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $nic, $user_id); // Bind NIC and user ID
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $complaint = $result->fetch_assoc(); // Fetch the complaint data
        } else {
            $error_message = "No complaint found for NIC: " . htmlspecialchars($nic) . " under your account.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sexual Abuse Report Form</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
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
    <?php elseif ($complaint): ?>
        <div class="success-message">
            <h2>Complaint Details</h2>
            <p><strong>Case ID:</strong> <?= $complaint['id']; ?></p>
            <p><strong>Reporter:</strong> <?= $complaint['victim_full_name']; ?></p>
            <p><strong>Status:</strong> <?= isset($complaint['action']) ? $complaint['action'] : 'Not available'; ?></p>
        </div>
    <?php endif; ?>


    <div class="content">
        <h1>Report Sexual Abuse</h1>
        <form method="POST" action="submit_report.php" enctype="multipart/form-data">
            <!-- Complainant Information -->
            <h3>Your Information</h3>
            <label for="complainant-title">Title (Mr/Mrs/Ms):</label>
            <select id="complainant-title" name="complainant_title">
                <option value="Mr">Mr</option>
                <option value="Mrs">Mrs</option>
                <option value="Ms">Ms</option>
            </select>

            <label for="complainant-full-name">Full Name:</label>
            <input type="text" id="complainant-full-name" name="complainant_full_name" required>

            <label for="complainant-address">Address:</label>
            <input type="text" id="complainant-address" name="complainant_address" required>

            <label for="complainant-nic">NIC Number:</label>
<input type="text" id="complainant-nic" name="complainant_nic" value="<?php echo htmlspecialchars($nic); ?>" required>

            <label for="complainant-mobile">Mobile Number:</label>
            <input type="text" id="complainant-mobile" name="complainant_mobile" required>

            <label for="complainant-email">Email Address:</label>
            <input type="email" id="complainant-email" name="complainant_email" required>

            <label for="complainant-age">Age:</label>
            <input type="number" id="complainant-age" name="complainant_age" required>

            <label for="complainant-gender">Gender:</label>
            <input type="radio" id="complainant-male" name="complainant_gender" value="Male"> Male
            <input type="radio" id="complainant-female" name="complainant_gender" value="Female"> Female

            <!-- Victim Information -->
            <h3>Victim Information</h3>
            <label for="victim-full-name">Victim's Full Name:</label>
            <input type="text" id="victim-full-name" name="victim_full_name" required>

            <label for="victim-age">Victim's Age:</label>
            <input type="number" id="victim-age" name="victim_age" required>

            <label for="victim-gender">Victim's Gender:</label>
            <input type="radio" id="victim-male" name="victim_gender" value="Male"> Male
            <input type="radio" id="victim-female" name="victim_gender" value="Female"> Female

            <!-- Incident Details -->
            <h3>Incident Details</h3>
            <label for="occurrence-date">Date of Occurrence:</label>
            <input type="date" id="occurrence-date" name="occurrence_date" required>

            <label for="place-of-occurrence">Place of Occurrence:</label>
            <input type="text" id="place-of-occurrence" name="place_of_occurrence" required>

            <label for="description">Description of the Incident:</label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <label for="evidence-attachment">Attach Supporting Documents (if any):</label>
            <input type="file" id="evidence-attachment" name="evidence_attachment" accept="image/*,application/pdf">

            <input type="submit" value="Submit Report">
        </form>
    </div>

    <?php
   // include 'footer.php'; // Include footer
    ?>
</body>
</html>
