<?php
session_start();


// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crime_management";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Fetch clearance requests
$query = "SELECT * FROM police_clearance_applications  ORDER BY submitted_at DESC";
$result = mysqli_query($conn, $query);

// Handle the accept/reject request
if (isset($_POST['action'])) {
    $clearance_id = $_POST['clearance_id'];
    $status = $_POST['status'];

    // Update the status of the clearance request
    $update_query = "UPDATE police_clearance_applications  SET status = '$status' WHERE id = $clearance_id";
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Request updated successfully'); window.location.href = 'manage_clearance.php';</script>";
    } else {
        echo "<script>alert('Failed to update request');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Clearance Requests</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS here -->
</head>
<body>

    <div class="container">
        <h1>Manage Clearance Requests</h1>

        <!-- Clearance request table -->
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Current Address</th>
                    <th>Nationality</th>
                    <th>ID Number</th>
                    <th>Occupation</th>
                    <th>Employer</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['full_name']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['phone']; ?></td>
                        <td><?php echo $row['current_address']; ?></td>
                        <td><?php echo $row['nationality']; ?></td>
                        <td><?php echo $row['id_number']; ?></td>
                        <td><?php echo $row['occupation']; ?></td>
                        <td><?php echo $row['employer_name']; ?></td>
                        <td>
                            <span><?php echo ucfirst($row['status']); ?></span>
                        </td>
                        <td>
                            <!-- Accept and Reject buttons -->
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="clearance_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="status" value="accepted">
                                <button type="submit" name="action" value="accept">Accept</button>
                            </form>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="clearance_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" name="action" value="reject">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</body>
</html>
