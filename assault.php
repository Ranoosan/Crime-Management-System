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

// Initialize variables
$success_message = '';
$error_message = '';
$complaint = null;
// Fetch user's NIC number from the database based on user_id
$sql_nic = "SELECT nic_number FROM users WHERE id = ?";
$stmt_nic = $conn->prepare($sql_nic);
$stmt_nic->bind_param("i", $user_id);
$stmt_nic->execute();
$result_nic = $stmt_nic->get_result();

if ($result_nic->num_rows > 0) {
    $user_data = $result_nic->fetch_assoc();
    $user_nic_number = $user_data['nic_number'];
} else {
    $user_nic_number = ''; // Default if no NIC found
}
$stmt_nic->close();


// Check if the form for complaint submission is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data and sanitize it
    $reporter_title = $conn->real_escape_string(trim($_POST['reporter_title']));
    $victim_name = $conn->real_escape_string(trim($_POST['victim_name']));
    $address = $conn->real_escape_string(trim($_POST['address']));
    $nic_number = $conn->real_escape_string(trim($_POST['nic_number']));
    $district = $conn->real_escape_string(trim($_POST['district']));
    $mobile_number = $conn->real_escape_string(trim($_POST['mobile_number']));
    $age = $conn->real_escape_string(trim($_POST['age']));
    $gender = $conn->real_escape_string(trim($_POST['gender']));
    $occurrence_date = $conn->real_escape_string(trim($_POST['occurrence_date']));
    $place = $conn->real_escape_string(trim($_POST['place']));
    $complaint_text = $conn->real_escape_string(trim($_POST['complaint']));

    // Upload NIC images (front and back)
    $nic_front = $_FILES['nic_front'];
    $nic_back = $_FILES['nic_back'];

    $uploadDir = 'uploads/';
    $nic_front_path = $uploadDir . basename($nic_front['name']);
    $nic_back_path = $uploadDir . basename($nic_back['name']);

    move_uploaded_file($nic_front['tmp_name'], $nic_front_path);
    move_uploaded_file($nic_back['tmp_name'], $nic_back_path);

    // Insert the complaint into the database including user_id
    $sql = "INSERT INTO complaints (user_id, title, victim_name, address, nic_number, district, mobile_number, age, gender, occurrence_date, place, complaint, nic_front, nic_back, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssssssssss", $user_id, $reporter_title, $victim_name, $address, $nic_number, $district, $mobile_number, $age, $gender, $occurrence_date, $place, $complaint_text, $nic_front_path, $nic_back_path);

    if ($stmt->execute()) {
        // Get the ID of the last inserted complaint
        $complaint_id = $conn->insert_id;
        $success_message = "Complaint submitted successfully! Your Case ID is: " . $complaint_id;
    } else {
        $error_message = "Error submitting complaint: " . $conn->error;
    }

    // Close the prepared statement
    $stmt->close();
}

// Complaint status check form submission
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $case_id = $conn->real_escape_string(trim($_GET['id']));

        // Check if the case ID belongs to the logged-in user
        $result = $conn->query("SELECT * FROM complaints WHERE id = '$case_id' AND user_id = '$user_id'");
        if ($result->num_rows > 0) {
            $complaint = $result->fetch_assoc();
        } else {
            $error_message = "No complaint found with the provided Case ID or you do not have access.";
        }
    } elseif (isset($_GET['nic'])) {
        $nic_number = $conn->real_escape_string(trim($_GET['nic']));

        // Check if the NIC belongs to the logged-in user
        $result = $conn->query("SELECT * FROM complaints WHERE nic_number = '$nic_number' AND user_id = '$user_id'");
        if ($result->num_rows > 0) {
            $complaint = $result->fetch_assoc();
        } else {
            $error_message = "No complaint found with the provided NIC number or you do not have access.";
        }
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Complaint Form - Sri Lanka Citizens</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            background-color: #ccc;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="radio"] {
            width: auto;
            margin-right: 5px;
        }

        button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .success-message,
        .error-message {
            text-align: center;
            margin-bottom: 15px;
        }

        .success-message {
            color: green;
        }

        .error-message {
            color: red;
        }

        /* Add this CSS to your stylesheet or within a <style> tag */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #333;
            color: white;
            padding: 10px;
            z-index: 1000;
        }

        .container {
            margin-top: 60px;
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
            <div class="error-message"><?= $error_message; ?></div>
        <?php elseif ($complaint): ?>
            <div class="success-message">
                <h2>Complaint Details</h2>
                <p><strong>Case ID:</strong> <?= $complaint['id']; ?></p>
                <p><strong>Reporter:</strong> <?= $complaint['victim_name']; ?></p>
                <p><strong>Incident Description:</strong> <?= $complaint['complaint']; ?></p>
                <p><strong>Status:</strong> <?= isset($complaint['action_level']) ? $complaint['action_level'] : 'Not available'; ?></p>
            </div>
        <?php endif; ?>

        <h2>Submit Your Complaint</h2>

        <!-- Display Success or Error Messages -->
        <?php if (isset($success_message)): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php elseif (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <!-- Complaint Form -->
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="reporter_title">Title:</label>
            <select id="reporter_title" name="reporter_title">
                <option value="Mr.">Mr.</option>
                <option value="Mrs.">Mrs.</option>
                <option value="Miss">Miss</option>
            </select>

            <label for="victim_name">Victim Name:</label>
            <input type="text" id="victim_name" name="victim_name" placeholder="Enter Victim's Name" required>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" placeholder="Enter Victim's Address" required>

            <label for="nic_number">NIC Number:</label>
<input type="text" id="nic_number" name="nic_number" value="<?= htmlspecialchars($user_nic_number); ?>" readonly required>

            <label for="district">District:</label>
            <input type="text" id="district" name="district" placeholder="Enter District" required>

            <label for="mobile_number">Mobile Number:</label>
            <input type="text" id="mobile_number" name="mobile_number" placeholder="Enter Mobile Number" required>

            <label for="age">Age:</label>
            <input type="text" id="age" name="age" placeholder="Enter Age" required>

            <label for="gender">Gender:</label>
            <select id="gender" name="gender">
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>

            <label for="occurrence_date">Occurrence Date:</label>
            <input type="date" id="occurrence_date" name="occurrence_date" required>

            <label for="place">Place of Incident:</label>
            <input type="text" id="place" name="place" placeholder="Enter Place of Incident" required>

            <label for="complaint">Complaint:</label>
            <textarea id="complaint" name="complaint" placeholder="Enter Complaint" rows="4" required></textarea>

            <label for="nic_front">NIC Front Image:</label>
            <input type="file" id="nic_front" name="nic_front" required>

            <label for="nic_back">NIC Back Image:</label>
            <input type="file" id="nic_back" name="nic_back" required>

            <button type="submit">Submit Complaint</button>
        </form>
    </div>
</body>

</html>
