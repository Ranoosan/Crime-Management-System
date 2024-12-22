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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Experiments</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        p {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        .experiment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .experiment-card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s;
        }

        .experiment-card:hover {
            transform: scale(1.05);
        }

        .experiment-card h3 {
            font-size: 1.5em;
            margin: 10px;
            color: #007BFF;
        }

        .experiment-card p {
            margin: 5px 10px;
            color: #555;
        }

        .experiment-images {
            display: flex;
            justify-content: center;
            margin: 10px 0;
        }

        .experiment-image {
            width: 100%;
            height: auto;
            max-width: 100%;
            border-radius: 4px;
            margin: 0 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .experiment-actions {
            margin-top: 10px;
            text-align: center;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            background-color: #007BFF; /* Primary button color */
            color: white;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s;
            margin: 5px;
        }

        .btn:hover {
            background-color: #0056b3; /* Darker shade on hover */
        }

        .delete {
            background-color: #dc3545; /* Danger button color */
        }

        .delete:hover {
            background-color: #c82333; /* Darker shade on hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Social Experiments</h1>
        <p>Explore various social experiments conducted in our community.</p>

        <?php if ($result->num_rows > 0): ?>
            <div class="experiment-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="experiment-card">
                        <h3><?php echo htmlspecialchars($row['venue']); ?></h3>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($row['date']); ?></p>
                        <p><strong>Time:</strong> <?php echo htmlspecialchars($row['time']); ?></p>
                        <p><strong>Guests:</strong> <?php echo htmlspecialchars($row['guests']); ?></p>
                        <div class="experiment-images">
                            <?php 
                            $images = explode(',', $row['images']);
                            foreach ($images as $image): 
                                if (!empty($image)): ?>
                                    <img src="<?php echo htmlspecialchars($image); ?>" alt="Image" class="experiment-image">
                                <?php endif; 
                            endforeach; 
                            ?>
                        </div>
                        
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No social experiments found.</p>
        <?php endif; ?>

    </div>

 
</body>
</html>

<?php
$conn->close();
?>
