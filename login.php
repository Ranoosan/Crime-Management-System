<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";  // replace with your db username
$password = "";      // replace with your db password
$dbname = "crime_management";  // replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$loginError = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nic_number = $_POST['nic_number'];
    $password = $_POST['password'];
    $gmail = $_POST['gmail'];

    // Sanitize input to avoid SQL injection
    $nic_number = mysqli_real_escape_string($conn, $nic_number);
    $password = mysqli_real_escape_string($conn, $password);
    $gmail = mysqli_real_escape_string($conn, $gmail);

    // Fetch the user from the database
    $sql = "SELECT * FROM users WHERE nic_number='$nic_number' AND email='$gmail'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Check if password matches
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Password is correct
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nic_number'] = $user['nic_number'];
            
            // Redirect to the profile page or home page
            header("Location: index.php");
            exit();
        } else {
            $loginError = "Invalid password.";
        }
    } else {
        $loginError = "No user found with that NIC number or Gmail.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 400px;
            margin: 100px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        h2 {
            text-align: center;
        }

        .login-form {
            display: flex;
            flex-direction: column;
        }

        .login-form label {
            margin-bottom: 5px;
        }

        .login-form input {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .login-button {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-button:hover {
            background-color: #218838;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

        .register-link {
            text-align: center;
            margin-top: 10px;
        }

        .register-link a {
            color: #007bff;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if ($loginError) : ?>
            <p class="error"><?= $loginError ?></p>
        <?php endif; ?>
        <form class="login-form" method="POST" action="login.php">
            <label for="nic_number">NIC Number:</label>
            <input type="text" id="nic_number" name="nic_number" placeholder="Enter your NIC number" required>

            <label for="gmail">Gmail:</label>
            <input type="email" id="gmail" name="gmail" placeholder="Enter your Gmail" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <button type="submit" class="login-button">Login</button>
        </form>

        <!-- Registration link for new users -->
        <div class="register-link">
            <p>New here? <a href="register.php">Register now</a></p>
        </div>
    </div>
</body>
</html>
