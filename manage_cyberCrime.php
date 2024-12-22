<?php
session_start();

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

// Update the status if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $complaint_id = $_POST['complaint_id'];
    $new_status = $_POST['status'];

    $sql = "UPDATE cyber_crime_complaints SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $complaint_id);

    if ($stmt->execute()) {
        $message = "Status updated successfully!";
    } else {
        $message = "Error updating status: " . $stmt->error;
    }

    $stmt->close();
}

// Delete complaint
if (isset($_GET['delete'])) {
    $complaint_id = $_GET['delete'];
    $sql = "DELETE FROM cyber_crime_complaints WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $complaint_id);

    if ($stmt->execute()) {
        $message = "Complaint deleted successfully!";
    } else {
        $message = "Error deleting complaint: " . $stmt->error;
    }

    $stmt->close();
}

// Search functionality
$search_query = '';
if (isset($_POST['search'])) {
    $search_query = $_POST['search_query'];
    $sql = "SELECT * FROM cyber_crime_complaints WHERE complainant_full_name LIKE ? OR id LIKE ? OR description LIKE ?";
    $stmt = $conn->prepare($sql);
    $like_query = '%' . $search_query . '%';
    $stmt->bind_param("sss", $like_query, $like_query, $like_query);
} else {
    // Fetch complaints from the database if no search is performed
    $sql = "SELECT * FROM cyber_crime_complaints";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cyber Crime Complaints</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin-left: 250px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        button {
            padding: 5px 10px;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .update-btn {
            background-color: #28a745;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .edit-btn {
            background-color: #17a2b8;
        }
        .update-btn:hover, .delete-btn:hover, .edit-btn:hover {
            opacity: 0.9;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
        }
        .status-update {
            display: flex;
            gap: 10px;
        }
        .search-bar {
            margin-bottom: 20px;
        }
        .search-bar input[type="text"] {
            padding: 8px;
            width: 80%;
        }
        .search-bar button {
            padding: 8px 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Include the sidebar -->
    <?php include 'sidebar.php'; ?>
    
    <div class="container">
        <h1>Manage Cyber Crime Complaints</h1>

        <?php if (isset($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <div class="search-bar">
            <form method="POST">
                <input type="text" name="search_query" placeholder="Search by name, ID or description" value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" name="search">Search</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Complainant Name</th>
                    <th>Occurrence Date</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Update Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['complainant_full_name'] . "</td>";
                        echo "<td>" . $row['occurrence_date'] . "</td>";
                        echo "<td>" . $row['description'] . "</td>";
                        echo "<td>" . $row['status'] . "</td>";
                        echo '<td>
                                <form method="POST" class="status-update">
                                    <input type="hidden" name="complaint_id" value="' . $row['id'] . '">
                                    <select name="status">
                                        <option value="Pending"' . ($row['status'] == 'Pending' ? ' selected' : '') . '>Pending</option>
                                        <option value="In Progress"' . ($row['status'] == 'In Progress' ? ' selected' : '') . '>In Progress</option>
                                        <option value="Closed"' . ($row['status'] == 'Closed' ? ' selected' : '') . '>Closed</option>
                                    </select>
                                    <button type="submit" name="update_status" class="update-btn">Update</button>
                                </form>
                              </td>';
                        echo '<td>
                                <a href="view_managecybercrime_complaint.php?id=' . $row['id'] . '" class="edit-btn">View</a>
                                <a href="edit_complaint.php?id=' . $row['id'] . '" class="edit-btn">Edit</a>
                                <a href="?delete=' . $row['id'] . '" class="delete-btn" onclick="return confirm(\'Are you sure you want to delete this complaint?\')">Delete</a>
                              </td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No complaints found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Close connection
$conn->close();
?>
