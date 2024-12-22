<?php
session_start();

// Check if already logged in
if (isset($_SESSION['lawyer_id'])) {
    header('Location: lawyer_dashboard.php');
    exit();
}

$loginError = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the submitted credentials
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Default credentials for the lawyer
    $defaultUsername = 'lawyer';
    $defaultPassword = 'lawyer123'; // In a real application, this would be hashed

    if ($username === $defaultUsername && $password === $defaultPassword) {
        // Set session variable for lawyer
        $_SESSION['lawyer_id'] = 3; // Assuming the ID for this lawyer
        header('Location: lawyer_dashboard.php');
        exit();
    } else {
        $loginError = 'Invalid username or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lawyer Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 300px;
        }
        .login-container h2 {
            margin: 0 0 20px;
        }
        .login-container input {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .login-container button:hover {
            background-color: #218838;
        }
        .login-container .error {
            color: red;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Lawyer Login</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
            <?php if ($loginError): ?>
                <p class="error"><?php echo htmlspecialchars($loginError); ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
