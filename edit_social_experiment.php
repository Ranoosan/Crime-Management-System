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

// Get the experiment ID from the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Fetch the experiment data
    $sql = "SELECT * FROM social_experiments WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $experiment = $result->fetch_assoc();
    } else {
        echo "Experiment not found.";
        exit;
    }
} else {
    echo "No experiment ID provided.";
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $venue = $_POST['venue'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $guests = $_POST['guests'];
    $images = $_POST['images']; // Assuming a comma-separated list of image URLs

    $sql = "UPDATE social_experiments SET venue = ?, date = ?, time = ?, guests = ?, images = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $venue, $date, $time, $guests, $images, $id);

    if ($stmt->execute()) {
        echo "Social experiment updated successfully!";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Social Experiment</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="content">
        <h2>Edit Social Experiment</h2>
        <form action="edit_social_experiment.php?id=<?php echo $id; ?>" method="post">
            <label for="venue">Venue:</label><br>
            <input type="text" id="venue" name="venue" value="<?php echo htmlspecialchars($experiment['venue']); ?>" required><br>

            <label for="date">Date:</label><br>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($experiment['date']); ?>" required><br>

            <label for="time">Time:</label><br>
            <input type="time" id="time" name="time" value="<?php echo htmlspecialchars($experiment['time']); ?>" required><br>

            <label for="guests">Guests:</label><br>
            <input type="text" id="guests" name="guests" value="<?php echo htmlspecialchars($experiment['guests']); ?>" required><br>

            <label for="images">Image URLs (comma-separated):</label><br>
            <input type="text" id="images" name="images" value="<?php echo htmlspecialchars($experiment['images']); ?>"><br>

            <button type="submit">Update Experiment</button>
        </form>
    </div>

    <?php include 'footer.php'; // Include footer ?>
</body>
</html>
