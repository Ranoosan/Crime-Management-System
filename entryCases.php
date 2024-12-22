<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crime_management";  // Update this if necessary

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $case_number = mysqli_real_escape_string($conn, $_POST['case_number']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $age = mysqli_real_escape_string($conn, $_POST['age']);
    $nic_number = mysqli_real_escape_string($conn, $_POST['nic_number']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Validate the age (Ensure it's a number)
    if (!is_numeric($age)) {
        $message = "Age must be a valid number.";
    } else {
        // Insert data into the database
        $sql = "INSERT INTO entryCases (case_number, name, age, nic_number, status) 
                VALUES ('$case_number', '$name', '$age', '$nic_number', '$status')";

        if (mysqli_query($conn, $sql)) {
            $message = "Case entry added successfully!";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }

    // Close the connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Case Entry</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        .message {
            margin-top: 20px;
            color: green;
            font-size: 18px;
        }
    </style>
</head>
<body>

    <h1>Add New Case</h1>

    <!-- Display message if form was submitted -->
    <?php if (!empty($message)) { echo "<p class='message'>$message</p>"; } ?>

    <!-- Case Entry Form -->
    <form action="entryCases.php" method="POST">
        <label for="case_number">Case Number</label>
        <input type="text" id="case_number" name="case_number" required>

        <label for="name">Name</label>
        <input type="text" id="name" name="name" required>

        <label for="age">Age</label>
        <input type="number" id="age" name="age" required>

        <label for="nic_number">NIC Number</label>
        <input type="text" id="nic_number" name="nic_number" required>

        <label for="status">Status</label>
        <select id="status" name="status" required>
            <option value="released">Released</option>
            <option value="pending">Pending</option>
        </select>

        <input type="submit" value="Submit">
    </form>

</body>
</html>
