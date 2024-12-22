<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'crime_management');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$id = $email = $password = $created_at = $nic_number = $profile_picture = '';

// Check if an ID is provided
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch user data
    $sql = "SELECT email, password, created_at, nic_number, profile_picture FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['email'];
        $password = $row['password'];
        $created_at = $row['created_at'];
        $nic_number = $row['nic_number'];
        $profile_picture = $row['profile_picture'];
    } else {
        echo "User not found.";
        exit();
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $nic_number = $_POST['nic_number'];
    $profile_picture = $_POST['profile_picture'];

    // Update user data
    $sql = "UPDATE users SET email = ?, password = ?, nic_number = ?, profile_picture = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $email, $password, $nic_number, $profile_picture, $id);

    if ($stmt->execute()) {
        header("Location: manage_users.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit User</h1>
        <form method="POST" action="">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>" required>

            <label for="nic_number">NIC Number:</label>
            <input type="text" id="nic_number" name="nic_number" value="<?php echo htmlspecialchars($nic_number); ?>" required>

            <label for="profile_picture">Profile Picture URL:</label>
            <input type="text" id="profile_picture" name="profile_picture" value="<?php echo htmlspecialchars($profile_picture); ?>">

            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
