<?php
// Start the session
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_email'])) {
    // Redirect to login page if not logged in
    header("Location: adminS2login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crime_management";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch admin details from session
$admin_email = $_SESSION['admin_email'];

// Fetch the admin details from the database
$sql = "SELECT * FROM adminS2_users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $admin = $result->fetch_assoc();
} else {
    echo "Admin not found.";
    exit();
}

// Close statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .dashboard-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #333;
        }
        .admin-info {
            margin-bottom: 20px;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .admin-info p {
            margin: 5px 0;
            font-size: 16px;
        }
        .dashboard-menu {
            margin: 20px 0;
        }
        .dashboard-menu a {
            display: inline-block;
            margin: 10px 15px;
            text-decoration: none;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .dashboard-menu a:hover {
            background-color: #45a049;
        }
        .logout {
            margin-top: 20px;
            display: block;
            padding: 10px 20px;
            background-color: #f44336;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .logout:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Welcome to the Admin Dashboard</h1>

        <div class="admin-info">
            <p><strong>IC Number:</strong> <?php echo htmlspecialchars($admin['ic_number']); ?></p>
            <p><strong>Email (Gmail):</strong> <?php echo htmlspecialchars($admin['email']); ?></p>
        </div>

        <div class="dashboard-menu">
           
            <a href="lawWriter_page.php">LAW</a>
            <a href="history_writer.php">HISTORY</a>
            <a href="social_experiment_page.php">SOCIAL EXPERIMENT CONTEST</a>
            <a href="staff_details_page.php">STAFF</a>
            <a href="vacancies_dash.php">VACANCIES</a>
        </div>

        <!-- <a class="logout" href="logout.php">Log Out</a> -->
    </div>
</body>
</html>
