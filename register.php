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
$nic_number = $gmail = $password = $confirm_password = "";
$registrationError = "";

// If the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nic_number = $_POST['nic_number'];
    $gmail = $_POST['gmail'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    if ($password !== $confirm_password) {
        $registrationError = "Passwords do not match.";
    } else {
        // Sanitize inputs
        $nic_number = mysqli_real_escape_string($conn, $nic_number);
        $gmail = mysqli_real_escape_string($conn, $gmail);
        $password = mysqli_real_escape_string($conn, $password);

        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into the database
        $sql = "INSERT INTO users (nic_number, email, password) VALUES ('$nic_number', '$gmail', '$hashed_password')";

        if ($conn->query($sql) === TRUE) {
            // Redirect to login page after successful registration
            header("Location: login.php");
            exit();
        } else {
            $registrationError = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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

        .register-form {
            display: flex;
            flex-direction: column;
        }

        .register-form label {
            margin-bottom: 5px;
        }

        .register-form input {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .register-button {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .register-button:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <?php if ($registrationError) : ?>
            <p class="error"><?= $registrationError ?></p>
        <?php endif; ?>
        <form class="register-form" method="POST" action="register.php">
            <label for="nic_number">NIC Number:</label>
            <input type="text" id="nic_number" name="nic_number" placeholder="Enter your NIC number" required>

            <label for="gmail">Gmail:</label>
            <input type="email" id="gmail" name="gmail" placeholder="Enter your Gmail" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>

            <button type="submit" class="register-button">Register</button>
        </form>
    </div>
</body>
</html>
