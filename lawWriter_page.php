<?php
session_start();

// Only allow access if the admin is logged in
if (!isset($_SESSION['admin_email'])) {
    header("Location: adminS2login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Law Writer Page</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .lawWriter-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 400px;
        }
        h2 {
            color: #333;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 15px 20px;
            margin: 10px 0;
            width: 100%;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="lawWriter-container">
        <h2>Law Writer Dashboard</h2>

        <!-- Button to go to Law Write Page -->
        <button onclick="window.location.href='Law_writer.php'">Law Write</button>

        <!-- Button to go to Edit Laws Page -->
        <button onclick="window.location.href='edit_laws.php'">Edit Laws</button>

        <!-- Button to go to View Added Laws Page -->
        <button onclick="window.location.href='view_laws.php'">View Added Laws</button>
    </div>
</body>
</html>
