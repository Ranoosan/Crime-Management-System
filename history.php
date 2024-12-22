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

// Fetch all history notes
$sql = "SELECT * FROM history_notes ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History of Sri Lankan Police</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->

    <style>
        body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    background-color: #f4f4f4;
    color: #333;
    margin: 0;
    padding: 20px;
}

.content {
    max-width: 800px;
    margin: auto;
    background: #fff;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

h2 {
    text-align: center;
    color: #2c3e50;
}

.history-notes {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.note-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
}

.note-card:hover {
    transform: translateY(-5px);
}

.note-card h3 {
    font-size: 1.5em;
    margin-bottom: 10px;
    color: #2980b9;
}

.note-card img {
    max-width: 100%;
    height: auto;
    border-radius: 4px;
}

.note-card small {
    display: block;
    margin-bottom: 10px;
    color: #777;
}

.note-card p {
    margin: 10px 0 0;
}

    </style>
</head>
<body>
    <div class="content">
        <h2>History of Sri Lankan Police</h2>
        
        <?php if ($result->num_rows > 0): ?>
            <div class="history-notes">
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="note-card">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <small>Posted on <?php echo htmlspecialchars($row['created_at']); ?></small>
                        <?php if ($row['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="History Image">
                        <?php endif; ?>
                        <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No history notes found.</p>
        <?php endif; ?>

    </div>

<?php include 'footer.php'; // Include footer ?>
</body>
</html>

<?php
$conn->close();
?>
