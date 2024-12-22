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

// Generate CSRF token if not already set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Fetch case details using Case ID
    if (!empty($_GET['id'])) {
        $case_id = ($_GET['id']);

        $query = "SELECT * FROM missing_person_reports WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $case_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $complaint = $result->fetch_assoc();
        } else {
            $error_message = "No complaint found for Case ID: " . htmlspecialchars($case_id) . " under your account.";
        }
    }
    // Fetch case details using NIC
    elseif (!empty($_GET['nic'])) {
        $nic = $conn->real_escape_string($_GET['nic']);

        $query = "SELECT * FROM missing_person_reports WHERE complainant_nic = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $nic, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $complaint = $result->fetch_assoc();
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
    <title>Report Missing Person</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <!-- Complaint Status Section -->
    <h2>Check Complaint Status</h2>

    <!-- Case ID Input Form -->
    <form action="" method="GET">
        <label for="case_id">Case ID:</label>
        <input type="text" id="case_id" name="id" placeholder="Enter Case ID" required>
        <input type="submit" value="View Complaint">
    </form>

    <!-- NIC Input Form -->
    <form action="" method="GET">
        <label for="complainant_nic">NIC Number:</label>
        <input type="text" id="complainant_nic" name="nic" placeholder="Enter NIC Number" required>
        <input type="submit" value="View Complaint">
    </form>

    <!-- Display Error or Complaint Details -->
    <?php if (!empty($error_message)): ?>
        <div class="error-message"><?= $error_message; ?></div>
    <?php elseif (!empty($complaint)): ?>
        <div class="success-message">
            <h2>Complaint Details</h2>
            <p><strong>Case ID:</strong> <?= htmlspecialchars($complaint['id']); ?></p>
            <p><strong>Reporter:</strong> <?= htmlspecialchars($complaint['complainant_full_name']); ?></p>
            <p><strong>Status:</strong> <?= isset($complaint['action']) ? htmlspecialchars($complaint['action']) : 'Not available'; ?></p>
        </div>
    <?php endif; ?>

    <!-- Report Missing Person Form -->
    <div class="content">
        <h1>Report Missing Person</h1>
        <form method="POST" action="submit_missing_person.php" enctype="multipart/form-data">

            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

            <!-- Complainant Information -->
            <h3>Your Information</h3>
            <label for="complainant-full-name">Your Full Name:</label>
            <input type="text" id="complainant-full-name" name="complainant_full_name" required>

            <label for="complainant-address">Your Address:</label>
            <input type="text" id="complainant-address" name="complainant_address" required>

            <!-- Automatically fill NIC from session -->
            <label for="complainant_nic">NIC Number</label>
            <input type="text" name="complainant_nic" id="complainant_nic" value="<?= $nic ?>" readonly>


            <label for="complainant-mobile">Your Mobile Number:</label>
            <input type="text" id="complainant-mobile" name="complainant_mobile" title="Enter a valid 10-digit mobile number" required>

            <label for="complainant-email">Your Email Address:</label>
            <input type="email" id="complainant-email" name="complainant_email" required>

            <!-- Missing Person Information -->
            <h3>Missing Person Information</h3>
            <label for="missing-full-name">Missing Person's Full Name:</label>
            <input type="text" id="missing-full-name" name="missing_full_name" required>

            <label for="missing-age">Missing Person's Age:</label>
            <input type="number" id="missing-age" name="missing_age" min="1" required>

            <label for="missing-gender">Missing Person's Gender:</label>
            <input type="radio" id="missing-male" name="missing_gender" value="Male" required> Male
            <input type="radio" id="missing-female" name="missing_gender" value="Female"> Female
            <input type="radio" id="missing-other" name="missing_gender" value="Other"> Other

            <label for="missing-description">Description of Missing Person:</label>
            <textarea id="missing-description" name="missing_description" rows="4" required></textarea>

            <label for="last-seen-date">Last Seen Date:</label>
            <input type="date" id="last-seen-date" name="last_seen_date" required>

            <label for="last-seen-location">Last Seen Location:</label>
            <input type="text" id="last-seen-location" name="last_seen_location" required placeholder="Enter the address or provide a link to a map">

            <label for="map-url">Map URL (if available):</label>
            <input type="url" id="map-url" name="map_url" placeholder="https://www.google.com/maps/">

            <label for="incident-description">Incident Description:</label>
            <textarea id="incident-description" name="incident_description" rows="4" required></textarea>

            <label for="evidence-attachment">Attach Supporting Documents (if any):</label>
            <input type="file" id="evidence-attachment" name="evidence_attachment" accept="image/*,application/pdf">

            <input type="submit" value="Submit Report">
        </form>
    </div>

    <?php
    include 'footer.php'; // Include footer
    ?>
</body>

</html>