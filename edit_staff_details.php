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

// Initialize variables for form data
$staff = [
    'id' => '',
    'fullname' => '',
    'position' => '',
    'born_at' => '',
    'place' => '',
    'experience' => '',
    'gender' => '',
    'image' => ''
];

// Handle the editing logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['id'];
    $fullname = $_POST['fullname'];
    $position = $_POST['position'];
    $born_at = $_POST['born_at'];
    $place = $_POST['place'];
    $experience = $_POST['experience'];
    $gender = $_POST['gender'];
    
    // Handle image upload
    if ($_FILES['image']['name']) {
        $target_dir = "uploads/"; // Directory where images will be stored
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image = $target_file;
    } else {
        // Keep the existing image if no new image is uploaded
        $sql = "SELECT image FROM staff_details WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $image = $row['image'];
        }
        $stmt->close();
    }

    // Update staff member in the database
    $sql = "UPDATE staff_details SET fullname=?, position=?, born_at=?, place=?, experience=?, gender=?, image=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $fullname, $position, $born_at, $place, $experience, $gender, $image, $id);
    $stmt->execute();
    $stmt->close();
    
    // Redirect to staff details page after updating
    header("Location: staff_details_page.php");
    exit();
}

// Fetch the staff member details from the database based on the ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM staff_details WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $staff = $result->fetch_assoc();
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff Details</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .content {
            max-width: 600px;
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
        input[type="text"], input[type="file"], select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="content">
        <h2>Edit Staff Details</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($staff['id']); ?>">
            <label for="fullname">Full Name:</label>
            <input type="text" name="fullname" value="<?php echo htmlspecialchars($staff['fullname']); ?>" required>
            
            <label for="position">Position:</label>
            <input type="text" name="position" value="<?php echo htmlspecialchars($staff['position']); ?>" required>
            
            <label for="born_at">Born At:</label>
            <input type="text" name="born_at" value="<?php echo htmlspecialchars($staff['born_at']); ?>" required>
            
            <label for="place">Place:</label>
            <input type="text" name="place" value="<?php echo htmlspecialchars($staff['place']); ?>" required>
            
            <label for="experience">Experience:</label>
            <input type="text" name="experience" value="<?php echo htmlspecialchars($staff['experience']); ?>" required>
            
            <label for="gender">Gender:</label>
            <select name="gender" required>
                <option value="Male" <?php echo $staff['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo $staff['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                <option value="Other" <?php echo $staff['gender'] == 'Other' ? 'selected' : ''; ?>>Other</option>
            </select>
            
            <label for="image">Image:</label>
            <input type="file" name="image" accept="image/*">

            <input type="submit" name="update" value="Update Staff">
        </form>
    </div>

    <?php include 'footer.php'; // Include footer ?>
</body>
</html>

<?php
$conn->close();
?>
