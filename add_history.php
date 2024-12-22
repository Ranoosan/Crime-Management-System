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
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    // Handle the file upload
    if (isset($_FILES['image'])) {
        $image = $_FILES['image'];
        
        // Check for errors
        if ($image['error'] == 0) {
            // Specify the directory to save the uploaded file
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($image["name"]);
            
            // Move the uploaded file to the target directory
            if (move_uploaded_file($image["tmp_name"], $target_file)) {
                // Insert the note into the database with the file path
                $sql = "INSERT INTO history_notes (title, content, image_url) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $title, $content, $target_file);

                if ($stmt->execute()) {
                    echo "History note added successfully!";
                } else {
                    echo "Error: " . $stmt->error;
                }

                $stmt->close();
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "No image uploaded.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add History Note</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="content">
        <h2>Add History Note</h2>
        <form action="add_history.php" method="post" enctype="multipart/form-data"> <!-- Added enctype for file uploads -->
            <label for="title">Title:</label><br>
            <input type="text" id="title" name="title" required><br>

            <label for="content">Content:</label><br>
            <textarea id="content" name="content" rows="4" required></textarea><br>

            <label for="image">Image Upload:</label><br> <!-- Changed to file upload -->
            <input type="file" id="image" name="image" accept="image/*" required><br> <!-- Only allow image files -->

            <button type="submit">Add Note</button>
        </form>
    </div>

    <?php include 'footer.php'; // Include footer ?>
</body>
</html>
