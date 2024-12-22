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

// Fetch staff members from the database
$sql = "SELECT * FROM staff_details";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Details</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .content {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .staff-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }
        .staff-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin: 15px;
            padding: 20px;
            width: 300px;
            text-align: center;
        }
        .staff-card img {
            width: 100%;
            height: auto;
            border-radius: 50%;
        }
        .staff-card h3 {
            margin: 10px 0;
            color: #007BFF;
        }
        .staff-card p {
            color: #555;
            margin: 5px 0;
        }
        .action-buttons {
            margin-top: 10px;
        }
        .action-buttons a {
            text-decoration: none;
            color: white;
            background-color: #007BFF;
            padding: 8px 15px;
            border-radius: 5px;
            margin: 5px;
        }
        .action-buttons a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="content">
        <h2>Staff Details</h2>
        <p>Information about the staff members.</p>
        
        <div class="staff-container">
            <?php if ($result->num_rows > 0): ?>
                <?php while($staff = $result->fetch_assoc()): ?>
                    <div class="staff-card">
                        <img src="<?php echo htmlspecialchars($staff['image']); ?>" alt="<?php echo htmlspecialchars($staff['fullname']); ?>">
                        <h3><?php echo htmlspecialchars($staff['fullname']); ?></h3>
                        <p><strong>Position:</strong> <?php echo htmlspecialchars($staff['position']); ?></p>
                        <p><strong>Born At:</strong> <?php echo htmlspecialchars($staff['born_at']); ?></p>
                        <p><strong>Place:</strong> <?php echo htmlspecialchars($staff['place']); ?></p>
                        <p><strong>Experience:</strong> <?php echo htmlspecialchars($staff['experience']); ?> years</p>
                        <p><strong>Gender:</strong> <?php echo htmlspecialchars($staff['gender']); ?></p>
                       
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No staff members found.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; // Include footer ?>
</body>
</html>

<?php
$conn->close();
?>
