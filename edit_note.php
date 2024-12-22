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

// Check if ID is set in the URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Get ID from URL

    // Fetch the note with the specified ID
    $sql = "SELECT * FROM law_notes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the note exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $title = $row['title'];
        $note = $row['note'];
        $image_url = $row['image_url'];
    } else {
        echo "Note not found.";
        exit();
    }
} else {
    echo "No note ID provided.";
    exit();
}

// Handle form submission for updating the note
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $updated_title = $_POST['title'];
    $updated_note = $_POST['note'];
    $updated_image_url = $_POST['image_url']; // Assuming image URL is provided

    // Update the note in the database
    $update_sql = "UPDATE law_notes SET title = ?, note = ?, image_url = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $updated_title, $updated_note, $updated_image_url, $id);

    if ($update_stmt->execute()) {
        echo "Note updated successfully!";
        header("Location: view_laws.php"); // Redirect to the view page
        exit();
    } else {
        echo "Error updating note: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Law Note</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="content">
        <h2>Edit Law Note</h2>

        <!-- Form for editing the law note -->
        <form action="" method="POST">
            <label for="title">Title:</label><br>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required><br><br>

            <label for="note">Note:</label><br>
            <textarea id="note" name="note" rows="5" required><?php echo htmlspecialchars($note); ?></textarea><br><br>

            <label for="image_url">Image URL (optional):</label><br>
            <input type="text" id="image_url" name="image_url" value="<?php echo htmlspecialchars($image_url); ?>"><br><br>

            <input type="submit" value="Update Note">
        </form>
    </div>
</body>
</html>
