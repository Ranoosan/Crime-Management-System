<?php
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

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM staff_details WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: staff_details_page.php"); // Redirect after deletion
    exit();
}

// Fetch all staff details from the database
$sql = "SELECT * FROM staff_details ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Details</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .content {
            max-width: 1000px;
            margin: auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        .btn {
            padding: 8px 12px;
            margin: 5px;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            text-decoration: none;
        }
        .edit {
            background-color: #28a745;
        }
        .delete {
            background-color: #dc3545;
        }
        .add-btn {
            background-color: #007BFF;
            display: block;
            margin: 20px auto;
            width: 200px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="content">
        <h2>Staff Details</h2>
        <a href="add_staff_details.php" class="btn add-btn">Add Staff</a>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Position</th>
                        <th>Born At</th>
                        <th>Place</th>
                        <th>Experience</th>
                        <th>Gender</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($row['position']); ?></td>
                            <td><?php echo htmlspecialchars($row['born_at']); ?></td>
                            <td><?php echo htmlspecialchars($row['place']); ?></td>
                            <td><?php echo htmlspecialchars($row['experience']); ?></td>
                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Image" style="width: 50px; height: auto;"></td>
                            <td>
                                <a href="edit_staff_details.php?id=<?php echo $row['id']; ?>" class="btn edit">Edit</a>
                                <a href="?delete_id=<?php echo $row['id']; ?>" class="btn delete" onclick="return confirm('Are you sure you want to delete this staff member?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No staff details found.</p>
        <?php endif; ?>

    </div>

    <?php include 'footer.php'; // Include footer ?>
</body>
</html>

<?php
$conn->close();
?>
