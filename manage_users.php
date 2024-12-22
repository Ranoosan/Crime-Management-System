<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'crime_management');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize search query
$search_query = '';

// Check if a search was performed
if (isset($_GET['search_query'])) {
    $search_query = $conn->real_escape_string($_GET['search_query']);
}

// Delete user function
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        header("Location: manage_users.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Fetch all users from the database based on the search query
$sql = "SELECT id, email, password, created_at, nic_number, profile_picture FROM users
        WHERE email LIKE '%$search_query%' OR nic_number LIKE '%$search_query%'
        ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex; /* Ensures that the sidebar and content are aligned side by side */
        }

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

        .container {
            margin-left: 270px; /* Adds enough space for the sidebar */
            max-width: calc(100% - 270px); /* Reduces width to accommodate sidebar */
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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

        .search-form {
            margin-bottom: 20px;
            text-align: center;
        }

        .search-form input[type="text"] {
            padding: 10px;
            width: calc(100% - 90px);
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .search-form button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-form button:hover {
            opacity: 0.9;
        }
    </style>
</head>

<body>
    <!-- Include the sidebar -->
    <?php include 'sidebar.php'; ?>

    <div class="container">
        <h1>Manage Users</h1>

        <!-- Search Form -->
        <form class="search-form" method="GET" action="manage_users.php">
            <input type="text" name="search_query" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search by Email or NIC Number">
            <button type="submit">Search</button>
        </form>

        <!-- Display Users -->
        <table>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Password</th>
                <th>Created At</th>
                <th>NIC Number</th>
                <th>Profile Picture</th>
                <th>Actions</th>
            </tr>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['password']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($row['nic_number']); ?></td>
                        <td>
                            <?php if ($row['profile_picture']): ?>
                                <img src="<?php echo htmlspecialchars($row['profile_picture']); ?>" alt="Profile Picture" width="50">
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td class="action-buttons">
                            <a href="edit_user.php?id=<?php echo $row['id']; ?>">Edit</a>
                            <a href="?delete_id=<?php echo $row['id']; ?>" class="delete-button" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No users found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

</body>

</html>

<?php
$conn->close();
?>
