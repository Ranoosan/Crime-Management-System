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

// Check if 'id' is passed in the URL
if (isset($_GET['id'])) {
    $application_id = intval($_GET['id']);

    // Fetch the current application details
    $application_sql = "SELECT a.id, a.user_id, a.vacancy_id, a.application_date, a.cv_file, a.mobile_number, v.job_title
                         FROM job_applications_with_cv a
                         JOIN vacancies v ON a.vacancy_id = v.id
                         JOIN users u ON a.user_id = u.id
                         WHERE a.id = ?";

    $stmt = $conn->prepare($application_sql);
    $stmt->bind_param("i", $application_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $application = $result->fetch_assoc();
    } else {
        echo "Application not found.";
        exit();
    }
    $stmt->close();
} else {
    echo "Invalid application ID.";
    exit();
}

// Handle form submission to update application
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $vacancy_id = $_POST['vacancy_id'];
    $mobile_number = $_POST['mobile_number'];

    // Update application details
    $update_sql = "UPDATE job_applications_with_cv 
                   SET user_id = ?, vacancy_id = ?, mobile_number = ?
                   WHERE id = ?";

    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("iisi", $user_id, $vacancy_id, $mobile_number, $application_id);

    if ($stmt->execute()) {
        echo "<script>alert('Application updated successfully'); window.location.href='admin_view_applications.php';</script>";
    } else {
        echo "<script>alert('Error updating application');</script>";
    }
    $stmt->close();
}

// Fetch job vacancies for dropdown
$vacancies_sql = "SELECT id, job_title FROM vacancies WHERE status = 'open'";
$vacancies_result = $conn->query($vacancies_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job Application</title>
</head>
<body>
    <h2>Edit Job Application</h2>
    <form action="edit_application.php?id=<?php echo $application['id']; ?>" method="POST">
        <label for="user_id">User ID:</label>
        <input type="text" name="user_id" value="<?php echo $application['user_id']; ?>" required>
        <br><br>

        <label for="vacancy_id">Job Vacancy:</label>
        <select name="vacancy_id" required>
            <?php
            // Pre-fill the current vacancy selection
            while ($vacancy = $vacancies_result->fetch_assoc()) {
                $selected = ($vacancy['id'] == $application['vacancy_id']) ? 'selected' : '';
                echo "<option value='" . $vacancy['id'] . "' $selected>" . htmlspecialchars($vacancy['job_title']) . "</option>";
            }
            ?>
        </select>
        <br><br>

        <label for="mobile_number">Mobile Number:</label>
        <input type="text" name="mobile_number" value="<?php echo $application['mobile_number']; ?>" required>
        <br><br>

        <input type="submit" value="Update Application">
    </form>

    <br>
    <a href="admin_view_applications.php">Back to Applications</a>
</body>
</html>

<?php
$conn->close();
?>
