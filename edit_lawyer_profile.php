<?php
// Start the session
session_start();

// Ensure the lawyer is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login_lawyers.php"); // Redirect to login if not logged in
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "crime_management"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the lawyer's email from session
$lawyer_email = $_SESSION['email'];

// Fetch the lawyer's details
$query = "SELECT * FROM lawyer_registrations WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $lawyer_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $lawyer_details = $result->fetch_assoc();
} else {
    echo "<p>No registered profile found.</p>";
    exit();
}

// Check if form is submitted to update the profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nic = $_POST['nic'];
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $area_of_practice = $_POST['area_of_practice'];
    $experience = $_POST['experience'];
    $address = $_POST['address'];

    // Update the lawyer's profile in the database
    $update_query = "UPDATE lawyer_registrations SET nic = ?, display_name = ?, contact_numbers = ?, area_of_practice = ?, experience = ?, address = ? WHERE email = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sssssss", $nic, $name, $contact, $area_of_practice, $experience, $address, $lawyer_email);

    if ($update_stmt->execute()) {
        echo "<p>Profile updated successfully!</p>";
        header("Refresh: 2; URL=lawyer_profile.php"); // Redirect back after update
        exit();
    } else {
        echo "Error updating profile: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lawyer Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            margin-top: 20px;
        }

        form input[type="text"], form input[type="tel"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        form input[type="submit"] {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form input[type="submit"]:hover {
            background-color: #218838;
        }

        .profile-details {
            margin-top: 20px;
        }

        .profile-details p {
            font-size: 18px;
            color: #555;
            line-height: 1.6;
        }

        .profile-details p span {
            font-weight: bold;
        }

        .profile-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .profile-details table, .profile-details th, .profile-details td {
            border: 1px solid #ccc;
        }

        .profile-details th, .profile-details td {
            padding: 10px;
            text-align: left;
        }

        .profile-details th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Edit Profile</h1>

        <form method="POST" action="">
            <label for="nic">NIC:</label>
            <input type="text" id="nic" name="nic" value="<?php echo htmlspecialchars($lawyer_details['nic']); ?>" required>

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($lawyer_details['display_name']); ?>" required>

            <label for="contact">Contact Number:</label>
            <input type="tel" id="contact" name="contact" value="<?php echo htmlspecialchars($lawyer_details['contact_numbers']); ?>" required>

            <label for="area_of_practice">Area of Practice:</label>
            <input type="text" id="area_of_practice" name="area_of_practice" value="<?php echo htmlspecialchars($lawyer_details['area_of_practice']); ?>" required>

            <label for="experience">Experience:</label>
            <input type="text" id="experience" name="experience" value="<?php echo htmlspecialchars($lawyer_details['experience']); ?>" required>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($lawyer_details['address']); ?>" required>

            <input type="submit" value="Update Profile">
        </form>

        <div class="profile-details">
            <h2>Your Current Profile Details:</h2>
            <table>
                <tr>
                    <th>Field</th>
                    <th>Details</th>
                </tr>
                <tr>
                    <td>Full Name</td>
                    <td><?php echo htmlspecialchars($lawyer_details['display_name']); ?></td>
                </tr>
                <tr>
                    <td>NIC</td>
                    <td><?php echo htmlspecialchars($lawyer_details['nic']); ?></td>
                </tr>
                <tr>
                    <td>Contact Number</td>
                    <td><?php echo htmlspecialchars($lawyer_details['contact_numbers']); ?></td>
                </tr>
                <tr>
                    <td>Area of Practice</td>
                    <td><?php echo htmlspecialchars($lawyer_details['area_of_practice']); ?></td>
                </tr>
                <tr>
                    <td>Experience</td>
                    <td><?php echo htmlspecialchars($lawyer_details['experience']); ?></td>
                </tr>
                <tr>
                    <td>Address</td>
                    <td><?php echo htmlspecialchars($lawyer_details['address']); ?></td>
                </tr>
            </table>
        </div>

    </div>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
