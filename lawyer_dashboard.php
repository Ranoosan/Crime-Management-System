<?php
session_start();

// Check if the lawyer is logged in
if (!isset($_SESSION['lawyer_id'])) {
    header('Location: lawyer_login.php');
    exit();
}

// Dashboard content goes here
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lawyer Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .dashboard-container {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .dashboard-container h1 {
            margin: 0 0 20px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Welcome to the Lawyer Dashboard</h1>
        <p>You are logged in as a lawyer. Here you can manage your cases and view reports.</p>
        <!-- Add more content for the dashboard here -->
    </div>
</body>
</html>
