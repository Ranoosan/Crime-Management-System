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

// Fetch all law notes
$sql = "SELECT * FROM law_notes ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Law Education Notes</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .content {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
        }
        .note {
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fefefe;
        }
        .note h3 {
            color: #4CAF50;
        }
        .note p {
            line-height: 1.6;
        }
        .note img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            margin-top: 10px;
        }
        small {
            display: block;
            margin-top: 10px;
            color: #777;
        }
        hr {
            border: none;
            height: 1px;
            background: #ddd;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="content">
        <h2>Law Education Notes</h2>
        <?php
        if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<div class='note'>";
                echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                echo "<p>" . nl2br(htmlspecialchars($row['note'])) . "</p>";
                if ($row['image_url']) {
                    echo "<img src='" . htmlspecialchars($row['image_url']) . "' alt='Law Image'>";
                }
                echo "<small>Posted on " . htmlspecialchars($row['created_at']) . "</small>";
                echo "</div><hr>";
            }
        } else {
            echo "<p>No notes found.</p>";
        }

        $conn->close();
        ?>
    </div>

    <?php include 'footer.php'; // Include footer ?>
</body>
</html>
