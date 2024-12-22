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

// SQL query to fetch job applications with the user's details
$applications_sql = "SELECT a.id AS application_id, u.name, u.email, u.phone, u.current_address, 
                          v.job_title, v.job_description, a.application_date 
                    FROM job_applications a
                    JOIN users u ON a.user_id = u.id
                    JOIN vacancies v ON a.vacancy_id = v.id
                    WHERE a.vacancy_id IN (2, 3)  -- Only display applications related to vacancies 2 and 3
                    ORDER BY a.application_date DESC";
$applications_result = $conn->query($applications_sql);

// Check if the query was successful
if (!$applications_result) {
    die("Error executing query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Applicants</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        td {
            background-color: #f9f9f9;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .actions {
            display: flex;
            justify-content: space-between;
        }

        .action-btn {
            margin: 5px;
            padding: 5px 10px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        .action-btn:hover {
            background-color: #0056b3;
        }

        .call-now {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
        }

        .call-now:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="content">
        <h1>Job Applicants</h1>

        <?php
        // Display job applications
        if ($applications_result->num_rows > 0) {
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Applicant Name</th>';
            echo '<th>Email</th>';
            echo '<th>Phone</th>';
            echo '<th>Current Address</th>';
            echo '<th>Job Title</th>';
            echo '<th>Job Description</th>';
            echo '<th>Application Date</th>';
            echo '<th>Actions</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            while ($row = $applications_result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                echo '<td>' . htmlspecialchars($row['phone']) . '</td>';
                echo '<td>' . htmlspecialchars($row['current_address']) . '</td>';
                echo '<td>' . htmlspecialchars($row['job_title']) . '</td>';
                echo '<td>' . htmlspecialchars($row['job_description']) . '</td>';
                echo '<td>' . htmlspecialchars($row['application_date']) . '</td>';
                echo '<td>
                        <a href="tel:' . $row['phone'] . '" class="call-now">Call Now</a>
                    </td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
        } else {
            echo "<p>No applicants found for the selected vacancies.</p>";
        }

        // Close the database connection
        $conn->close();
        ?>
    </div>
</body>
</html>
