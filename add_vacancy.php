<?php
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crime_management"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



// Handle form submission for adding a vacancy
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $job_title = $_POST['job_title'];
    $job_description = $_POST['job_description'];
    $requirements = $_POST['requirements'];
    $status = $_POST['status'];

    // Validate the form fields (basic validation)
    if (empty($job_title) || empty($job_description) || empty($requirements)) {
        echo "<script>alert('Please fill in all fields.');</script>";
    } else {
        // Insert the vacancy into the database
        $sql = "INSERT INTO vacancies (job_title, job_description, requirements, status, created_at) 
                VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $job_title, $job_description, $requirements, $status);
        if ($stmt->execute()) {
            echo "<script>alert('Vacancy added successfully!');</script>";
        } else {
            echo "<script>alert('Error adding vacancy.');</script>";
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
    <title>Add Vacancy</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-group textarea {
            height: 150px;
        }

        .form-group button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .form-group button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<h2>Add a New Vacancy</h2>

<div class="form-container">
    <form action="add_vacancy.php" method="POST">
        <div class="form-group">
            <label for="job_title">Job Title</label>
            <input type="text" id="job_title" name="job_title" required>
        </div>
        
        <div class="form-group">
            <label for="job_description">Job Description</label>
            <textarea id="job_description" name="job_description" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="requirements">Requirements</label>
            <textarea id="requirements" name="requirements" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                <option value="open">Open</option>
                <option value="closed">Closed</option>
            </select>
        </div>
        
        <div class="form-group">
            <button type="submit">Add Vacancy</button>
        </div>
    </form>
</div>

</body>
</html>
