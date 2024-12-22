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

// Fetch all social experiments from the database
$sql = "SELECT * FROM social_experiments ORDER BY created_at DESC";
$result = $conn->query($sql);

// Handle deletion of a social experiment
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Prepare delete statement
    $delete_sql = "DELETE FROM social_experiments WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $delete_id);

    if ($delete_stmt->execute()) {
        echo "<script>alert('Social experiment deleted successfully!'); window.location.href='social_experiment_page.php';</script>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $delete_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Experiments</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="content">
        <h2>Social Experiments</h2>
        
        <!-- Button to write a new social experiment -->
        <a href="social_experiment_writer.php" class="btn">Write Social Experiment</a>

        <h3>Existing Social Experiments</h3>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Venue</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Guests</th>
                        <th>Images</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['venue']); ?></td>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo htmlspecialchars($row['time']); ?></td>
                            <td><?php echo htmlspecialchars($row['guests']); ?></td>
                            <td>
                                <?php 
                                $images = explode(',', $row['images']);
                                foreach ($images as $image): 
                                    if (!empty($image)): ?>
                                        <img src="<?php echo htmlspecialchars($image); ?>" alt="Image" width="50" height="50">
                                    <?php endif; 
                                endforeach; 
                                ?>
                            </td>
                            <td>
                                <a href="edit_social_experiment.php?id=<?php echo $row['id']; ?>" class="btn">Edit</a>
                                <a href="social_experiment_page.php?delete_id=<?php echo $row['id']; ?>" class="btn" onclick="return confirm('Are you sure you want to delete this experiment?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No social experiments found.</p>
        <?php endif; ?>

    </div>

    <?php include 'footer.php'; // Include footer ?>
</body>
</html>

<?php
$conn->close();
?>
