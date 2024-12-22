<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please log in to apply for a job.";
    exit();
}

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

// Fetch job vacancies for the user to apply
$vacancies_sql = "SELECT id, job_title FROM vacancies WHERE status = 'open'";
$vacancies_result = $conn->query($vacancies_sql);

// If the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['vacancy_id']) && isset($_FILES['cv_file']) && isset($_POST['mobile_number'])) {
    $user_id = $_SESSION['user_id'];
    $vacancy_id = $_POST['vacancy_id'];
    $mobile_number = $_POST['mobile_number'];  // Mobile number from form

    // File upload
    $cv_file = $_FILES['cv_file'];
    $cv_name = basename($cv_file['name']);
    $cv_tmp = $cv_file['tmp_name'];
    $cv_folder = 'uploads/cvs/';
    
    // Ensure the 'uploads/cvs/' directory exists
    if (!is_dir($cv_folder)) {
        mkdir($cv_folder, 0777, true);
    }
    
    $cv_path = $cv_folder . $cv_name;

    // Validate file type (CV should be PDF)
    if ($cv_file['type'] == 'application/pdf') {
        if (move_uploaded_file($cv_tmp, $cv_path)) {
            // Insert application into the database
            $sql = "INSERT INTO job_applications_with_cv (user_id, vacancy_id, application_date, cv_file, mobile_number) 
                    VALUES (?, ?, NOW(), ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiss", $user_id, $vacancy_id, $cv_name, $mobile_number);
            
            if ($stmt->execute()) {
                // Alert the user that the application was successful
                echo "<script>alert('You have successfully applied for the job. Our team will contact you within 2 days.');</script>";
            } else {
                echo "<script>alert('Failed to apply for the job.');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Failed to upload the CV.');</script>";
        }
    } else {
        echo "<script>alert('Only PDF files are allowed for CV.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Application</title>
</head>
<body>
    <h2>Apply for Job</h2>
    <form action="apply_for_job.php" method="POST" enctype="multipart/form-data">
        <label for="vacancy_id">Select Job Vacancy:</label>
        <select name="vacancy_id" required>
            <?php
            if ($vacancies_result->num_rows > 0) {
                while ($vacancy = $vacancies_result->fetch_assoc()) {
                    echo "<option value='" . $vacancy['id'] . "'>" . htmlspecialchars($vacancy['job_title']) . "</option>";
                }
            } else {
                echo "<option>No vacancies available.</option>";
            }
            ?>
        </select>
        <br><br>

        <label for="mobile_number">Enter Your Mobile Number:</label>
        <input type="text" name="mobile_number" required>
        <br><br>

        <label for="cv_file">Upload Your CV (PDF only):</label>
        <input type="file" name="cv_file" accept=".pdf" required>
        <br><br>

        <input type="submit" value="Apply">
    </form>
</body>
</html>
