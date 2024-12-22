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

// Handle delete request
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']); // Sanitize the input
    $delete_sql = "DELETE FROM missing_person_reports WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $delete_id);

    if ($delete_stmt->execute()) {
        echo "<script>alert('Report deleted successfully.'); window.location.href='manage_missingperson.php';</script>";
    } else {
        echo "<script>alert('Error deleting report: " . $delete_stmt->error . "');</script>";
    }
}

// Fetch missing person reports with search functionality
$sql = "SELECT * FROM missing_person_reports WHERE missing_full_name LIKE ? OR complainant_full_name LIKE ?";
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
    <title>Manage Missing Persons</title>
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
    </style>
</head>
<body>
    <div class="content">
        <h1>Manage Missing Person Reports</h1>

        <!-- Search Bar -->
        <div class="search-container">
            <form method="POST">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by Missing Person or Complainant Name">
                <button type="submit">Search</button>
            </form>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Missing Person's Name</th>
                        <th>Complainant's Name</th>
                        <th>Last Seen Location</th>
                        <th>Last Seen Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['missing_full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['complainant_full_name']); ?></td>
                            <td>
                                <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($row['last_seen_location']); ?>" target="_blank">
                                    <?php echo htmlspecialchars($row['last_seen_location']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($row['last_seen_date']); ?></td>
                            <td>
                                <a href="viewmanage_missingPerson.php?id=<?php echo $row['id']; ?>">View</a>
                                <a href="edit_report.php?id=<?php echo $row['id']; ?>">Edit</a>
                                <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this report?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No missing person reports found.</p>
        <?php endif; ?>

        <a href="submit_missing_person.php">Report a New Missing Person</a>
    </div>

    <?php
    include 'footer.php'; // Include footer
    $conn->close(); // Close the database connection
    ?>
</body>
</html>
