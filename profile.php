<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Function to get user details from the database
function getUserDetails($user_id) {
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'crime_management');
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute the query
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result === false) {
        die("Execute failed: " . $stmt->error);
    }

    // Fetch user data
    $user = $result->fetch_assoc();
    
    $stmt->close();
    $conn->close();
    
    return $user;
}

// Handle profile picture upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['profile_picture'])) {
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($_FILES['profile_picture']['name']);
    
    // Check if the upload directory exists, create it if not
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Check if the file is an image
    $check = getimagesize($_FILES['profile_picture']['tmp_name']);
    if ($check !== false) {
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
            // Update the database with the new profile picture
            $conn = new mysqli('localhost', 'root', '', 'crime_management');
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            $sql = "UPDATE users SET profile_picture = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $uploadFile, $_SESSION['user_id']);
            $stmt->execute();
            $stmt->close();
            $conn->close();
            
            // Refresh the page to reflect the changes
            header("Location: profile.php");
            exit();
        } else {
            $uploadError = "Failed to upload file. Please check directory permissions.";
        }
    } else {
        $uploadError = "File is not an image.";
    }
}

// Get user details
$user = getUserDetails($_SESSION['user_id']);

// Check if user data is valid
if (!$user) {
    die("No user found with ID: " . $_SESSION['user_id']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css"> 
    <title>Profile - Batticaloa Police E-Crime Reporting System</title>
    <?php include 'navnbar.php'; ?>

    <style>
        /* Embedded CSS */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .profile-container {
            max-width: 900px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
            overflow: hidden;
        }

        .profile-container img {
            max-width: 150px;
            height: auto;
            border-radius: 50%;
            border: 3px solid #007bff;
            display: block;
            margin: 0 auto;
        }

        .profile-container h2 {
            text-align: center;
            margin-top: 0;
            color: #333;
        }

        .profile-container table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .profile-container th, .profile-container td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .profile-container th {
            background-color: #f4f4f4;
            color: #333;
        }

        .profile-container td {
            color: #666;
        }

        .profile-container form {
            margin-top: 30px;
            text-align: center;
        }

        .profile-container input[type="file"] {
            display: block;
            margin: 0 auto;
            margin-bottom: 10px;
        }

        .profile-container .upload-button {
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .profile-container .upload-button:hover {
            background-color: #0056b3;
        }

        .profile-container .edit-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .profile-container .edit-button:hover {
            background-color: #218838;
        }

        .error-message {
            color: #dc3545;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Profile Content -->
    <div class="profile-container">
        <h2>My Profile</h2>
        
        <!-- Display Profile Picture -->
        <img src="<?php echo htmlspecialchars($user['profile_picture'] ?: 'default-profile.png'); ?>" alt="Profile Picture">

        <!-- Profile Details -->
        <table>
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
            </tr>
            <tr>
                <th>NIC Number</th>
                <td><?php echo htmlspecialchars($user['nic_number']); ?></td>
            </tr>
            <tr>
                <th>Created At</th>
                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
            </tr>
        </table>

        <!-- Profile Picture Upload Form -->
        <form method="POST" enctype="multipart/form-data">
            <label for="profile_picture">Upload New Profile Picture:</label>
            <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
            <button type="submit" class="upload-button">Upload</button>
            <?php if (isset($uploadError)) : ?>
                <p style="color: red;"><?php echo htmlspecialchars($uploadError); ?></p>
            <?php endif; ?>
        </form>

        <a href="edit_profile.php" class="edit-button">Edit Profile</a>
    </div>
</body>
</html>
