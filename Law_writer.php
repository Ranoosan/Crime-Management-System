<?php
session_start();

// Only allow access if the admin is logged in
if (!isset($_SESSION['admin_email'])) {
    header("Location: adminS2login.php");
    exit();
}

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

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $note = $_POST['note'];
    $image_url = "";

    // Handle image upload
    if ($_FILES['image']['name']) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        } else {
            $message = "Sorry, there was an error uploading the image.";
        }
    }

    // Insert the note into the database
    $sql = "INSERT INTO law_notes (title, note, image_url) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $title, $note, $image_url);

    if ($stmt->execute()) {
        $message = "Note added successfully!";
    } else {
        $message = "Error: " . $conn->error;
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
    <title>Law Notes Writer</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS -->
</head>
<body>
    <div class="form-container">
        <h2>Add Law Notes</h2>
        <?php if ($message): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="Law_writer.php" method="post" enctype="multipart/form-data">
            <label for="title">Title:</label><br>
            <input type="text" id="title" name="title" required><br><br>

            <label for="note">Note:</label><br>
            <textarea id="note" name="note" rows="4" required></textarea><br><br>

            <label for="image">Upload Image:</label><br>
            <input type="file" id="image" name="image"><br><br>

            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
