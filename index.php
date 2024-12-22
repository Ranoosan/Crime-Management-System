<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'crime_management');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the user's complaints and check for notifications
$user_id = $_SESSION['user_id']; // Assume user ID is stored in the session after login

$sql = "SELECT * FROM complaints WHERE user_id = ? AND notify_user = TRUE";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Display alert if any notifications are found
    echo "<script>alert('Your complaint status has been updated. Please check the details.');</script>";
    
    // Mark notifications as seen by setting notify_user to FALSE
    $update_sql = "UPDATE complaints SET notify_user = FALSE WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $user_id);
    $update_stmt->execute();
    $update_stmt->close();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Batticaloa Police E-Crime Reporting System</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        nav {
            background-color: #333;
            padding: 1em;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav ul {
            list-style: none;
            padding: 0;
            display: flex;
        }

        nav ul li {
            margin-right: 15px;
            position: relative;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            display: block;
        }

        nav ul li:hover > ul {
            display: block;
        }

        nav ul ul {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #444;
            padding: 0;
            list-style: none;
        }

        nav ul ul li {
            width: 180px;
        }

        nav ul ul li a {
            padding: 10px;
            display: block;
            white-space: nowrap;
        }

        nav ul ul li a:hover {
            background-color: #555;
        }

        .nav-right {
            display: flex;
            align-items: center;
        }

        .login-button, .logout-button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }

        .login-button:hover, .logout-button:hover {
            background-color: #218838;
        }

        .logout-button {
            background-color: #dc3545;
        }

        .logout-button:hover {
            background-color: #c82333;
        }

        header {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 50px 0;
        }

        .button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }

        .features {
            display: flex;
            justify-content: space-around;
            margin: 20px;
        }

        .feature-box {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 25%;
        }
    </style>
</head>
<body>

    <!-- Include navigation bar -->
    <?php include 'navnbar.php'; ?>

    <!-- Main content -->
    <header>
        <h1>Welcome to Batticaloa Police E-Crime Reporting System</h1>
    </header>

    <div class="features">
        <div class="feature-box">
            <h2>File a Complaint</h2>
            <p>Report crimes directly to the Batticaloa Police with our easy-to-use online platform.</p>
            <a href="file_complaint.php" class="button">File Now</a>
        </div>
        <div class="feature-box">
            <h2>Check Complaint Status</h2>
            <p>Track the status of your filed complaints and stay updated.</p>
            <a href="view_complaint_public.php" class="button">Check Status</a>
        </div>
        <div class="feature-box">
            <h2>View Reports</h2>
            <p>Access reports related to your complaints and case details.</p>
            <a href="view_reports.php" class="button">View Reports</a>
        </div>
    </div>

    <!-- Include footer -->
    <?php include 'footer.php'; ?>
    
</body>
</html>
