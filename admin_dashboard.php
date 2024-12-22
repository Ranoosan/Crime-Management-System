<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}
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
            display: flex;
        }

        /* Sidebar styling */
        .sidebar {
            width: 250px;
            background-color: #007bff;
            padding: 20px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
        }

        .sidebar h1 {
            color: white;
            margin-bottom: 30px;
            font-size: 24px;
        }

        .sidebar a {
            background-color: #fff;
            color: #007bff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            margin-bottom: 10px;
            transition: background-color 0.3s, color 0.3s;
        }

        .sidebar a:hover {
            background-color: #0056b3;
            color: #fff;
        }

        /* Main content area */
        .content {
            margin-left: 250px; /* same width as the sidebar */
            padding: 40px;
            width: calc(100% - 250px);
            background-color: #f4f4f4;
            min-height: 100vh;
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .action-buttons a {
            padding: 8px 12px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 10px;
        }

        .action-buttons a:hover {
            background-color: #0056b3;
        }

        .delete-button {
            background-color: #dc3545;
        }

        .delete-button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

    <!-- Include the sidebar -->
    <?php include 'sidebar.php'; ?>
   

   <!-- Main Content -->
   <div class="content">
        <h1>Welcome to the Admin Dashboard</h1>
        
        <!-- Manage Clearance Button -->
        <a href="manage_clearance.php" class="manage-clearance-btn">Manage Clearance Requests</a>
        <a href="entryCases.php" class="manage-clearance-btn">Entry case </a>
        <a href="viewAddedCases.php" class="manage-clearance-btn">View cases </a>
        
    </div>

</body>
</html>
