<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'crime_management');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch complaint details
$id = intval($_GET['id']);
$sql = "SELECT action_level FROM child_abuse_complaints WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$complaint = $result->fetch_assoc();

if (!$complaint) {
    die("Complaint not found.");
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        .alert {
            padding: 15px;
            background-color: #f44336;
            color: white;
            margin-bottom: 20px;
        }
        .success {
            padding: 15px;
            background-color: #4CAF50;
            color: white;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'crime_management');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variable for complaint
$complaint = null;

// Check if an ID is provided via GET method
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Fetch complaint details from the database
    $sql = "SELECT action_level FROM child_abuse_complaints WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $complaint = $result->fetch_assoc();

    // If complaint not found
    if (!$complaint) {
        $error_message = "Complaint not found.";
    }

    // Close the statement and connection
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            opacity: 0.9;
        }
        .alert {
            padding: 15px;
            background-color: #f44336;
            color: white;
            margin-bottom: 20px;
        }
        .success {
            padding: 15px;
            background-color: #4CAF50;
            color: white;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
     
        <?php
        // If there's an error message (complaint not found)
        if (isset($error_message)) {
            echo "<div class='alert'>$error_message</div>";
        }

        // If complaint is found, display its status
        if ($complaint) {
            echo "<div class='container'>";
            echo "<h1>Complaint Status</h1>";
            if ($complaint['action_level'] == 'Pending') {
                echo "<div class='alert'>Your complaint is still under review and is marked as <strong>Pending</strong>.</div>";
            } elseif ($complaint['action_level'] == 'Taking Action') {
                echo "<div class='success'>Your complaint is being addressed, and action is currently being taken.</div>";
            } elseif ($complaint['action_level'] == 'Completed') {
                echo "<div class='success'>Your complaint has been <strong>Completed</strong>.</div>";
            }
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>

</body>
</html>
