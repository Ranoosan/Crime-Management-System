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

// Fetch the history note to edit
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM history_notes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $note = $result->fetch_assoc();
    } else {
        echo "No note found.";
        exit();
    }

    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image_url = $_POST['image_url'];

    $sql = "UPDATE history_notes SET title=?, content=?, image_url=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $title, $content, $image_url, $id);

    if ($stmt->execute()) {
        echo "History note updated successfully!";
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
    <title>Edit History Note</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="content">
        <h2>Edit History Note</h2>
        <form action="edit_history.php?id=<?php echo $id; ?>" method="post">
            <label for="title">Title:</label><br>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($note['title']); ?>" required><br>

            <label for="content">Content:</label><br>
            <textarea id="content" name="content" rows="4" required><?php echo htmlspecialchars($note['content']); ?></textarea><br>

            <label for="image_url">Image URL:</label><br>
            <input type="text" id="image_url" name="image_url" value="<?php echo htmlspecialchars($note['image_url']); ?>"><br>

            <button type="submit">Update Note</button>
        </form>
    </div>

    <?php include 'footer.php'; // Include footer ?>
</body>
</html>
