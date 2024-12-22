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

// Fetch job applications
$applications_sql = "SELECT a.id, a.user_id, a.vacancy_id, a.application_date, a.cv_file, a.mobile_number, v.job_title
                     FROM job_applications_with_cv a
                     JOIN vacancies v ON a.vacancy_id = v.id
                     JOIN users u ON a.user_id = u.id
                     ORDER BY a.application_date DESC";

// Execute the query
$applications_result = $conn->query($applications_sql);

// If there is an error with the query, handle it
if ($applications_result === false) {
    die("Error executing query: " . $conn->error);  // Display detailed error if the query fails
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Job Applications</title>
    <style>
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
    </style>
</head>
<body>
    <h2>Job Applications</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <!-- <th>Username</th> -->
                <th>Job Title</th>
                <th>Application Date</th>
                <th>CV File</th>
                <th>Mobile Number</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Check if any rows were returned
            if ($applications_result->num_rows > 0) {
                while ($application = $applications_result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$application['id']}</td>
                            <td>{$application['user_id']}</td>
                    
                            <td>{$application['job_title']}</td>
                            <td>{$application['application_date']}</td>
                            <td><a href='uploads/cvs/{$application['cv_file']}' target='_blank'>View CV</a></td>
                            <td>{$application['mobile_number']}</td>
                            <td>
                                <a href='edit_application.php?id={$application['id']}'>Edit</a> |
                          
                                <a href='delete_application.php?id={$application['id']}' onclick='return confirm(\"Are you sure you want to delete this application?\")'>Delete</a>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No applications found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
