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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $venue = $_POST['venue'];
    $time = $_POST['time'];
    $date = $_POST['date'];
    $guests = $_POST['guests'];

    // Handle file upload for images
    $image_urls = [];
    if (!empty($_FILES['images']['name'][0])) {
        $total_files = count($_FILES['images']['name']);
        for ($i = 0; $i < $total_files; $i++) {
            $file_name = $_FILES['images']['name'][$i];
            $target_dir = "uploads/"; // Make sure this directory exists and is writable
            $target_file = $target_dir . basename($file_name);
            
            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $target_file)) {
                $image_urls[] = $target_file;
            } else {
                echo "Error uploading file: " . htmlspecialchars($file_name);
            }
        }
    }

    // Save the image URLs as a comma-separated string
    $images = implode(',', $image_urls);

    // Insert into the database
    $sql = "INSERT INTO social_experiments (venue, time, date, images, guests) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $venue, $time, $date, $images, $guests);

    if ($stmt->execute()) {
        echo "Social experiment details added successfully!";
    } else {
        echo "Error: " . $stmt->error;
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
    <title>Add Social Experiment</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="content">
        <h2>Add Social Experiment</h2>
        <form action="social_experiment_writer.php" method="post" enctype="multipart/form-data">
            <label for="venue">Venue:</label><br>
            <input type="text" id="venue" name="venue" required><br>

            <label for="time">Time:</label><br>
            <input type="time" id="time" name="time" required><br>

            <label for="date">Date:</label><br>
            <input type="date" id="date" name="date" required><br>

            <label for="images">Upload Images:</label><br>
            <input type="file" id="images" name="images[]" multiple><br>

            <label for="guests">Guests:</label><br>
            <textarea id="guests" name="guests" rows="4" required></textarea><br>

            <button type="submit">Add Experiment</button>
        </form>
    </div>

    <?php include 'footer.php'; // Include footer ?>
</body>
</html>
