<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crime_management"; // Ensure this database exists

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize search variable
$search = "";
if (isset($_POST['search'])) {
    $search = $_POST['search'];
}

// Delete functionality
if (isset($_GET['delete'])) {
    $report_id = $_GET['delete'];
    $delete_sql = "DELETE FROM child_abuse_complaints WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $report_id);

    if ($stmt->execute()) {
        $message = "Report deleted successfully!";
    } else {
        $message = "Error deleting report: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch child abuse reports with search functionality
$sql = "SELECT * FROM child_abuse_complaints WHERE reporter_full_name LIKE ? OR victim_full_name LIKE ?";
$stmt = $conn->prepare($sql);
$likeSearch = "%" . $search . "%";
$stmt->bind_param("ss", $likeSearch, $likeSearch);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Child Abuse Reports</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        /* Add some styles for search and layout */
        .search-container {
            margin-bottom: 20px;
        }
        .search-container input[type="text"] {
            padding: 8px;
            width: 200px;
            margin-right: 10px;
        }
        .search-container button {
            padding: 8px;
        }
        /* Add this CSS to your existing styles.css or in the <style> section */
        body {
            display: flex;
        }

        .content {
            flex: 1;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            color: green;
        }

        a {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="content">
        <h1>Manage Child Abuse Reports</h1>

        <!-- Display message if set -->
        <?php if (isset($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Search Bar -->
        <div class="search-container">
            <form method="POST">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by Reporter or Victim Name">
                <button type="submit">Search</button>
            </form>
        </div>

        <!-- Table displaying child abuse reports -->
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Reporter Name</th>
                        <th>Reporter NIC</th>
                        <th>Victim Name</th>
                        <th>Incident Date</th>
                        <th>Incident Place</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['reporter_full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['reporter_nic']); ?></td>
                            <td><?php echo htmlspecialchars($row['victim_full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['incident_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['incident_place']); ?></td>
                            <td>
                                <a href="view_childAbuse_report.php?id=<?php echo $row['id']; ?>">View</a>
                                <a href="edit_childAbuse_report.php?id=<?php echo $row['id']; ?>">Edit</a>
                                <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this report?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No child abuse reports found.</p>
        <?php endif; ?>

        <a href="submit_childAbuse_report.php">Report a New Child Abuse Case</a>
    </div>

    <?php
    // Close database connection
    $conn->close();
    ?>
</body>
</html>
