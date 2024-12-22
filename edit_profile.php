<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$updateError = '';
$updateSuccess = '';

// Function to get user details from the database
function getUserDetails($user_id) {
    $conn = new mysqli('localhost', 'root', '', 'crime_management');
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    
    return $user;
}

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $nic_number = $_POST['nic_number'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password && $password !== $confirm_password) {
        $updateError = 'Passwords do not match.';
    } else {
        $conn = new mysqli('localhost', 'root', '', 'crime_management');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        if ($password) {
            // Hash the password if it's being updated
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET email = ?, nic_number = ?, password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $email, $nic_number, $hashed_password, $_SESSION['user_id']);
        } else {
            $sql = "UPDATE users SET email = ?, nic_number = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $email, $nic_number, $_SESSION['user_id']);
        }
        
        if ($stmt->execute()) {
            $updateSuccess = 'Profile updated successfully.';
        } else {
            $updateError = 'Failed to update profile. Please try again.';
        }
        
        $stmt->close();
        $conn->close();
    }
}

// Get user details
$user = getUserDetails($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css"> 
    <title>Edit Profile - Batticaloa Police E-Crime Reporting System</title>
    <?php include 'navnbar.php'; ?>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .edit-profile-container {
            max-width: 900px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
            overflow: hidden;
        }

        .edit-profile-container h2 {
            text-align: center;
            margin-top: 0;
            color: #333;
        }

        .edit-profile-container form {
            margin-top: 20px;
        }

        .edit-profile-container label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .edit-profile-container input[type="text"], .edit-profile-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .edit-profile-container button {
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .edit-profile-container button:hover {
            background-color: #0056b3;
        }

        .error-message, .success-message {
            color: #dc3545;
            text-align: center;
            margin-top: 20px;
        }

        .success-message {
            color: #28a745;
        }
    </style>
</head>
<body>
    <!-- Edit Profile Content -->
    <div class="edit-profile-container">
        <h2>Edit Profile</h2>
        
        <!-- Update Success/Error Messages -->
        <?php if ($updateError): ?>
            <p class="error-message"><?php echo htmlspecialchars($updateError); ?></p>
        <?php endif; ?>
        <?php if ($updateSuccess): ?>
            <p class="success-message"><?php echo htmlspecialchars($updateSuccess); ?></p>
        <?php endif; ?>
        
        <!-- Profile Update Form -->
        <form method="POST">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="nic_number">NIC Number:</label>
            <input type="text" id="nic_number" name="nic_number" value="<?php echo htmlspecialchars($user['nic_number']); ?>" required>

            <label for="password">New Password:</label>
            <input type="password" id="password" name="password">

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password">

            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>
</html>
