<?php
session_start();

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

// Handle delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delete_sql = "DELETE FROM complaints WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $message = "Complaint deleted successfully.";
    } else {
        $message = "Error deleting complaint: " . $conn->error;
    }
    
    $stmt->close();
}

// Fetch complaints based on the search query
$sql = "SELECT * FROM complaints WHERE 
        title LIKE '%$search_query%' OR 
        victim_name LIKE '%$search_query%' OR 
        occurrence_date LIKE '%$search_query%' OR 
        place LIKE '%$search_query%' 
        ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Complaints</title>
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
        .view-btn {
            background-color: #007bff;
        }
        .edit-btn {
            background-color: #28a745;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .view-btn:hover, .edit-btn:hover, .delete-btn:hover {
            opacity: 0.9;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
        }
        .search-form {
            margin-bottom: 20px;
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
        <h1>Manage Complaints</h1>

        <!-- Search Form -->
        <form class="search-form" method="GET" action="manage_complaints.php">
            <input type="text" name="search_query" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search by Title, Victim Name, Date, or Place">
            <button type="submit">Search</button>
        </form>

        <?php if (isset($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Victim Name</th>
                    <th>Date of Occurrence</th>
                    <th>Place of Occurrence</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['title']; ?></td>
                            <td><?php echo $row['victim_name']; ?></td>
                            <td><?php echo $row['occurrence_date']; ?></td>
                            <td><?php echo $row['place']; ?></td>
                            <td>
                                <a href="view_complaint.php?id=<?php echo $row['id']; ?>" class="view-btn">View</a>
                                <a href="edit_complaint.php?id=<?php echo $row['id']; ?>" class="edit-btn">Edit</a>
                                <a href="?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this complaint?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No complaints found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>
