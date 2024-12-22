<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "crime_management"; // replace with your actual database name

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$nic = $email = $display_name = $password = $confirm_password = "";
$nic_err = $email_err = $display_name_err = $password_err = $confirm_password_err = $register_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate NIC
    if (empty(trim($_POST["nic"]))) {
        $nic_err = "Please enter your NIC.";
    } else {
        $nic = trim($_POST["nic"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate display name
    if (empty(trim($_POST["display_name"]))) {
        $display_name_err = "Please enter your name.";
    } else {
        $display_name = trim($_POST["display_name"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm your password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if ($password != $confirm_password) {
            $confirm_password_err = "Passwords did not match.";
        }
    }

    // Check if NIC or email already exists
    if (empty($nic_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        $check_sql = "SELECT id FROM signup_lawyers WHERE nic = ? OR email = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("ss", $nic, $email);

        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $register_err = "A lawyer with this NIC or email already exists.";
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert new lawyer into database
                $insert_sql = "INSERT INTO signup_lawyers (nic, email, password, display_name) VALUES (?, ?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("ssss", $nic, $email, $hashed_password, $display_name);

                if ($insert_stmt->execute()) {
                    header("location: login_lawyers.php"); // Redirect to login page
                    exit();
                } else {
                    $register_err = "Oops! Something went wrong. Please try again later.";
                }
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lawyer Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-size: 16px;
            color: #555;
            display: block;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            border: none;
            background-color: #28a745;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .login-link {
            text-align: center;
            margin-top: 10px;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Lawyer Registration</h2>

        <?php
        if (!empty($register_err)) {
            echo "<p class='error'>$register_err</p>";
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="nic">NIC</label>
                <input type="text" id="nic" name="nic" value="<?php echo $nic; ?>" required>
                <span class="error"><?php echo $nic_err; ?></span>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
                <span class="error"><?php echo $email_err; ?></span>
            </div>

            <div class="form-group">
                <label for="display_name">Display Name</label>
                <input type="text" id="display_name" name="display_name" value="<?php echo $display_name; ?>" required>
                <span class="error"><?php echo $display_name_err; ?></span>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <span class="error"><?php echo $password_err; ?></span>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <span class="error"><?php echo $confirm_password_err; ?></span>
            </div>

            <div class="form-group">
                <input type="submit" value="Register">
            </div>
        </form>

        <div class="login-link">
            <p>Already have an account? <a href="login_lawyers.php">Login Now</a></p>
        </div>
    </div>

</body>

</html>
